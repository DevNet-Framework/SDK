<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Cli\Commands;

use Artister\System\Command\ICommand;
use Artister\System\Event\EventArgs;
use Artister\System\StringBuilder;
use Artister\System\ConsoleColor;
use Artister\System\Console;

class AddCommand implements ICommand
{
    public function execute(object $sender, EventArgs $event) : void
    {
        $namespace = "Application";
        $className = null;
        $basePath  = null;
        $arguments = $event->getAttribute('arguments');
        $template  = $arguments->getParameter('template');
        $help      = $arguments->getOption('--help');
        $name      = $arguments->getOption('--name');
        
        if ($help)
        {
            $this->showHelp();
        }
        
        if (!$template || !$template->Value)
        {
            Console::foreGroundColor(ConsoleColor::Red);
            Console::writeline("Template argument is missing!");
            Console::resetColor();
            exit;
        }

        if ($name)
        {
            $className = $name->Value;
        }

        $directory = $arguments->getOption('--directory');
        if ($directory)
        {
            $basePath = $directory->Value;
        }

        $templateName = $template->Value ?? '';
        $templateName = strtolower($templateName);
        $result       = null;

        switch ($templateName) {
            case 'class':
                $result = self::createClass($namespace, $className, $basePath);
                break;
            case 'controller':
                $result = self::createController($namespace, $className, $basePath);
                break;
            case 'entity':
                $result = self::createEntity($namespace, $className, $basePath);
                break;
            default:
                Console::foreGroundColor(ConsoleColor::Red);
                Console::writeline("The template {$templateName} not exist!");
                Console::resetColor();
                exit;
                break;
        }

        if (!$result)
        {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Somthing whent wrong! faild to create {$className} class.");
            Console::resetColor();
            exit;
        }

        Console::foregroundColor(ConsoleColor::Green);
        Console::writeline("The class {$className} was created successfully.");
        Console::resetColor();

        exit;
    }

    public static function createClass(string $namespace, ?string $className, ?string $basePath) : bool
    {
        $namespace   = implode("\\", [$namespace, $basePath]);
        $namespace   = str_replace("/", "\\", [$namespace, $basePath]);
        $namespace   = rtrim($namespace, "\\");
        $namespace   = ucwords($namespace, "\\");
        $className   = $className ?? "MyClass";
        $className   = ucfirst($className);
        $destination = implode("/", [getcwd(), $basePath]);

        $context = new StringBuilder();
        $context->appendLine("<?php");
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine("use Artister\System\Collections\ArrayList;");
        $context->appendLine("use Artister\System\Linq;");
        $context->appendLine();
        $context->appendLine("class {$className}");
        $context->appendLine("{");
        $context->appendLine("    public function __construct()");
        $context->appendLine("    {");
        $context->appendLine("        // code...");
        $context->appendLine("    }");
        $context->append("}");
        $context->appendLine();

        if (!is_dir($destination))
        {
            mkdir($destination, 0777, true);
        }

        $myfile = fopen($destination."/".$className.".php", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if (!$size || !$status)
        {
            return false;
        }

        return true;
    }

    public static function createController(string $namespace, ?string $className, ?string $basePath) : bool
    {
        $basePath    = $basePath ?? "Controllers";
        $namespace   = implode("\\", [$namespace, $basePath]);
        $namespace   = str_replace("/", "\\", [$namespace, $basePath]);
        $namespace   = rtrim($namespace, "\\");
        $namespace   = ucwords($namespace, "\\");
        $className   = $className ?? "MyController";
        $className   = ucfirst($className);
        $destination = implode("/", [getcwd(), $basePath]);

        $context = new StringBuilder();
        $context->appendLine("<?php");
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine("use Artister\Web\Mvc\Controller;");
        $context->appendLine("use Artister\Web\Mvc\IActionResult;");
        $context->appendLine();
        $context->appendLine("class {$className} extends Controller");
        $context->appendLine("{");
        $context->appendLine("    public function index() : IActionResult");
        $context->appendLine("    {");
        $context->appendLine("        return \$this->view();");
        $context->appendLine("    }");
        $context->append("}");
        $context->appendLine();

        if (!is_dir($destination))
        {
            mkdir($destination, 0777, true);
        }

        $myfile = fopen($destination."/".$className.".php", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if ($size && $status) {
            return true;
        }

        return false;
    }

    public static function createEntity(string $namespace, ?string $className, ?string $basePath) : bool
    {
        $basePath    = $basePath ?? "Models";
        $namespace   = implode("\\", [$namespace, $basePath]);
        $namespace   = str_replace("/", "\\", [$namespace, $basePath]);
        $namespace   = rtrim($namespace, "\\");
        $namespace   = ucwords($namespace, "\\");
        $className   = $className ?? "MyEntity";
        $className   = ucfirst($className);
        $destination = implode("/", [getcwd(), $basePath]);

        $context = new StringBuilder();
        $context->appendLine("<?php");
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine("use Artister\Entity\IEntity;");
        $context->appendLine();
        $context->appendLine("class {$className} implements IEntity");
        $context->appendLine("{");
        $context->appendLine("    private int \$Id;");
        $context->appendLine();
        $context->appendLine("    public function __get(string \$name)");
        $context->appendLine("    {");
        $context->appendLine("        return \$this->\$name;");
        $context->appendLine("    }");
        $context->appendLine();
        $context->appendLine("    public function __set(string \$name, \$value)");
        $context->appendLine("    {");
        $context->appendLine("        \$this->\$name = \$value;");
        $context->appendLine("    }");
        $context->append("}");
        $context->appendLine();

        if (!is_dir($destination))
        {
            mkdir($destination, 0777, true);
        }

        $myfile = fopen($destination."/".$className.".php", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if ($size && $status) {
            return true;
        }

        return false;
    }

    public function showHelp()
    {
        Console::writeline("Usage: devnet new [template] [arguments] [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help     Displays help for this command.");
        Console::writeline("  --project  Location to place the generated project.");
        Console::writeline();
        Console::writeline("templates:");
        Console::writeline("  class       Simple Class");
        Console::writeline("  controller  Controller Class");
        Console::writeline("  entity      Entity Class");
        Console::writeline();
        exit;
    }
}
