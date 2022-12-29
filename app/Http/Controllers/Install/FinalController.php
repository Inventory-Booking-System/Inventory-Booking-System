<?php

// Author: John Doe
// License: MIT
// Source: https://github.com/rashidlaasri/LaravelInstaller
// Note: This file has been modified to be included directly in the software rather than via a composer package

namespace App\Http\Controllers\Install;

use Illuminate\Routing\Controller;
use App\Events\LaravelInstallerFinished;
use App\Helpers\Install\EnvironmentManager;
use App\Helpers\Install\FinalInstallManager;
use App\Helpers\Install\InstalledFileManager;

class FinalController extends Controller
{
    /**
     * Update installed file and display finished view.
     *
     * @param \App\Helpers\Install\InstalledFileManager $fileManager
     * @param \App\Helpers\Install\FinalInstallManager $finalInstall
     * @param \App\Helpers\Install\EnvironmentManager $environment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment)
    {
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        $finalEnvFile = $environment->getEnvContent();

        event(new LaravelInstallerFinished);

        return view('install.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }
}
