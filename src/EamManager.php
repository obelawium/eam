<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam;

use Obelaw\Ium\Eam\Managers\AssetManager;
use Obelaw\Ium\Eam\Managers\CategoryManager;
use Obelaw\Ium\Eam\Managers\ReportsManager;
use Obelaw\Ium\Abstracts\Domain;

final class EamManager extends Domain
{
    public function assets(): AssetManager
    {
        return new AssetManager();
    }

    public function categories(): CategoryManager
    {
        return new CategoryManager();
    }

    public function reports(): ReportsManager
    {
        return new ReportsManager();
    }
}
