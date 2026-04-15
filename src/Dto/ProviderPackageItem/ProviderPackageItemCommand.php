<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto\ProviderPackageItem;
use Illuminate\Console\Command as LaravelCommand;

final class ProviderPackageItemCommand extends namespace\AbstractProviderPackageItem
{
    public readonly bool $consoleOnly;
    public function __construct(
        string|object $command,
        bool $consoleOnly = false,
    ){
        parent::__construct(
            class: $command,
            classIsSubOf: LaravelCommand::class,
        );
        $this->consoleOnly = $consoleOnly;
    }
}
