<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Dto\ProviderPackageItem\ProviderPackageItemMigration;
use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;
use Symfony\Component\Finder\Finder;

trait HasMigrations
{
    protected string $migrationsDirectory = 'database/migrations';
    protected bool $runsMigrations = false;
    protected bool $discoversMigrations = false;

    /** @var array<string, ProviderPackageItemMigration> */
    protected array $migrations = [];

    public function hasMigrations(): bool
    {
        return count($this->migrations) > 0;
    }

    public function getMigrations(): ?array
    {
        return $this->hasMigrations() ? $this->migrations : null;
    }
    public function getMigrationsDirectory(): string
    {
        return $this->migrationsDirectory;
    }

    public function runsMigrations(): bool
    {
        return $this->runsMigrations;
    }

    public function discoversMigrations(): bool
    {
        return $this->discoversMigrations;
    }

    public function getMigrationFilePath(
        string|\Stringable $fileName,
    ): ?string
    {
        return $this->hasBasePath() ? $this->getBasePath($this->getMigrationsDirectory(), $fileName) : null;
    }

    protected function validateMigrationsPath(
        mixed ...$parameters,
    ): void
    {
        $this->requireBasePathIf(true, ...$parameters,);

        $path = $this->getBasePath($this->getMigrationsDirectory());
        throw_if(
            !file_exists($path) || !is_dir($path),
            ProviderPackageException::class,
            sprintf('Migrations path `%s` must be an existing directory.', $path),
        );
    }

    public function setMigrationsDirectory(
        string|\Stringable $migrationsDirectory,
    ): static
    {
        $this->migrationsDirectory = $migrationsDirectory;
        $this->validateMigrationsPath(
            'Base path need to be set to set migrations directory.'
        );

        return $this;
    }

    public function setRunsMigrations(bool $enabled = true): static
    {
        if ($enabled) {
            $this->validateMigrationsPath(
                'Base path need to be set to enable runs migrations.'
            );
        }

        $this->runsMigrations = $enabled;

        return $this;
    }

    public function setDiscoversMigrations(
        bool $enabled = true,
        bool $publishable = true,
        array $publishGroups = [],
    ): static
    {
        if (!$enabled) {
            $this->migrations = array_filter(
                $this->migrations,
                static fn (ProviderPackageItemMigration $item) => !$item->isDiscovered
            );

            $this->discoversMigrations = false;
            return $this;
        }

        $this->validateMigrationsPath(
            'Base path need to be set to enable discovers migrations.'
        );

        $fileNames = Finder::create()
            ->in($this->getBasePath($this->getMigrationsDirectory()))
            ->name('*.php')
            ->depth('== 0')
            ->files()
            ->sortByName();

        foreach(array_keys(iterator_to_array($fileNames)) as $fileName) {
            $this->addMigrationInternally($fileName, $publishable, $publishGroups, true);
        }

        $this->discoversMigrations = true;

        return $this;
    }
    protected function addMigrationInternally(
        string|\Stringable $fileName,
        bool $publishable = true,
        array $publishGroups = [],
        bool $isDiscovered = false,
    ): static
    {
        $this->validateMigrationsPath(
            'Base path need to be set to to add migrations.'
        );

        $path = $this->getMigrationFilePath($fileName);

        if ($publishable && blank($publishGroups)){
            $publishGroups = [
                str($this->getName())->lower()->finish('-migrations')->value(),
            ];
        }

        $item = new ProviderPackageItemMigration(
            path: $path,
            publishGroups: $publishGroups,
            isDiscovered: $isDiscovered,
        );

        if (!array_key_exists($item->path, $this->migrations)){
            $this->migrations[$item->path] = $item;
        }

        return $this;
    }

    public function addMigration(
        string|\Stringable $fileName,
        bool $publishable = true,
        array $publishGroups = [],
    ): static
    {
        return $this->addMigrationInternally($fileName, $publishable, $publishGroups);
    }

    public function addMigrations(
        bool $publishable = true,
        array $publishGroups = [],
        string|\Stringable ...$fileNames,
    ): static
    {
        foreach($fileNames as $fileName){
           $this->addMigration($fileName, $publishable, $publishGroups);
        }

        return $this;
    }

//    public function discoversMigrations(bool $discoversMigrations = true, string $path = '/database/migrations'): static
//    {
//        $this->discoversMigrations = $discoversMigrations;
//        $this->migrationsPath = $path;
//
//        return $this;
//    }
}
