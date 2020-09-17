<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Sdk\Templates\Web;

use Artister\System\Cli\Parser\CommandParser;
use Artister\System\Runtime\Boot\LauncherProperties;
use Artister\System\StringBuilder;
use Artister\System\ConsoleColor;
use Artister\System\Console;

class Program
{
    public static function main(array $args = [])
    {
        $rootPath   = getcwd();
        $className  = "Program";
        $basePath   = null;
        $namespace  = LauncherProperties::getNamespace();

        $parser = new CommandParser();
        $parser->addOption('--name');
        $parser->addOption('--directory');
        $arguments = $parser->parse($args);

        $nameOption = $arguments->getOption('--name');
        if ($nameOption) {
            $className = $nameOption->Value;
        }

        if (!$className) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("class Name not found, maybe forget to enter class name using the option --main");
            Console::resetColor();
            exit;
        }

        $directoryOption = $arguments->getOption('--directory');
        if ($directoryOption) {
            $basePath = $directoryOption->Value;
        }

        $path = implode("/", [$rootPath, $basePath]);

        if (!is_dir($path)) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Invalid Path $path");
            Console::resetColor();
            exit;
        }

        $result = self::createProgram($path, $namespace, $className);

        if ($result)
        {
            self::copyFile( __DIR__.'/resources/Startup.php', $rootPath."/Startup.php");
            self::copyFile( __DIR__.'/resources/Routes.php', $rootPath."/Routes.php");
            self::copyFile( __DIR__.'/resources/settings.json', $rootPath."/settings.json");
            self::copyFile( __DIR__.'/resources/Controllers/HomeController.php', $rootPath."/Controllers/HomeController.php");
            self::copyFile( __DIR__.'/resources/Controllers/AccountController.php', $rootPath."/Controllers/AccountController.php");
            self::copyFile( __DIR__.'/resources/Views/home/index.phtml', $rootPath."/Views/home/index.phtml");
            self::copyFile( __DIR__.'/resources/Views/account/index.phtml', $rootPath."/Views/account/index.phtml");
            self::copyFile( __DIR__.'/resources/Views/account/login.phtml', $rootPath."/Views/account/login.phtml");
            self::copyFile( __DIR__.'/resources/Views/account/register.phtml', $rootPath."/Views/account/register.phtml");
            self::copyFile( __DIR__.'/resources/Views/layouts/layout.phtml', $rootPath."/Views/layouts/layout.phtml");
            self::copyFile( __DIR__.'/resources/Views/layouts/navbar.phtml', $rootPath."/Views/layouts/navbar.phtml");
            self::copyFile( __DIR__.'/resources/webroot/css/style.min.css', $rootPath."/webroot/css/style.css");
            self::copyFile( __DIR__.'/resources/webroot/lib/bootstrap/css/bootstrap.min.css', $rootPath."/webroot/lib/bootstrap/css/bootstrap.min.css");
            self::copyFile( __DIR__.'/resources/webroot/js/script.js', $rootPath."/webroot/js/script.js");
            self::copyFile( __DIR__.'/resources/webroot/lib/bootstrap/js/bootstrap.bundle.min.js', $rootPath."/webroot/lib/bootstrap/js/bootstrap.bundle.min.js");
            self::copyFile( __DIR__.'/resources/webroot/index.php', $rootPath."/webroot/index.php");
            self::copyFile( __DIR__.'/resources/webroot/.htaccess', $rootPath."/webroot/web.config");
            self::copyFile( __DIR__.'/resources/webroot/.htaccess', $rootPath."/webroot/.htaccess");

            Console::foregroundColor(ConsoleColor::Green);
            Console::writeline("The template 'Web Application' was created successfully.");
            Console::resetColor();
        }

    }

    public static function createProgram($path, $namespace, $className) : bool
    {
        $namespace  = ucwords($namespace, "\\");
        $className  = ucfirst($className);

        $context = new StringBuilder();
        $context->appendLine("<?php");
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine("use Artister\DevNet\Hosting\WebHost;");
        $context->appendLine("use Artister\DevNet\Hosting\IWebHostBuilder;");
        $context->appendLine();
        $context->appendLine("class {$className}");
        $context->appendLine("{");
        $context->appendLine("    public static function main(array \$args = [])");
        $context->appendLine("    {");
        $context->appendLine("        self::createWebHostBuilder(\$args)->build()->run();");
        $context->appendLine("    }");
        $context->appendLine();
        $context->appendLine("    public static function createWebHostBuilder(array \$args) : IWebHostBuilder");
        $context->appendLine("    {");
        $context->appendLine("        return WebHost::createBuilder(\$args)");
        $context->appendLine("            ->useStartup(Startup::class);");
        $context->appendLine("    }");
        $context->append("}");

        $myfile     = fopen($path."/".$className.".php", "w");
        $size       = fwrite($myfile, $context->__toString());
        $status     = fclose($myfile);

        if ($size && $status) {
            return true;
        }

        return false;
    }

    public static function copyFile($srcfile, $dstfile)
    {
        $desrir =  dirname($dstfile);
        if (!is_dir($desrir)) {
            mkdir($desrir, 0777, true);
        }

        if (!file_exists($dstfile)) {
            copy($srcfile, $dstfile);
        }
    }
}