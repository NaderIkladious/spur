<?php

namespace Spur\Spur;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class Spur
{
    public static function configIsNotPublished() {
        return is_null(config('spur'));
    }
    public static function components() {
        return config('spur.components');
    }
    public static function addComponentsToConfig(string $componentName) {
        $configFilePath = config_path('spur.php');
        $content = File::get($configFilePath);

        $pattern = "/(\s*)('components' => \[)(?<!\n)(.*?)(\h*)]/s";
        $replacement = "$1'components' => [$3$4$4'$componentName',$1]";
        $newContent = preg_replace($pattern, $replacement, $content);

        File::put($configFilePath, $newContent);
    }

    public static function downloadComponent(string $component, bool $force)
    {
        $componentParts = explode('/', $component);
        $componentFileName = end($componentParts);
        $directoryPath = resource_path("views/components/spur");
        $filePath = resource_path("views/components/spur/{$componentFileName}.blade.php");
        if (!File::exists($filePath) || $force) {
            $url = env('SPUR_SERVER_URL', 'https://app.spurui.dev');
            $path = '/api/get-component';
            $filename = $component.'.blade.php';
            $token = config('spur.token');
            File::makeDirectory($directoryPath, 0755, true, true);


            $templateRequest = Http::withToken($token)->post($url . $path, ['name' => $filename]);
            if ($templateRequest->successful() && $templateRequest->effectiveUri()->getPath() === $path) {
                file_put_contents($filePath, $templateRequest->body());
                return true;
            }
        }
        return false;
    }
    public static function isComponentAlreadyAdded(string $component) {
        return in_array($component, config('spur.components'));
    }
}