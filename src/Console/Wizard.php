<?php

namespace AMWScan\Console;

use AMWScan\Scanner;

class Wizard
{
    public function run()
    {
        CLI::displayTitle('Setup Wizard', 'cyan', 'black');
        CLI::newLine();

        // 1. Path to scan
        $path = CLI::read('Path to scan [Default: ./]: ', 'green');
        if (empty($path)) {
            $path = './';
        }
        Scanner::setPathScan($path);
        CLI::newLine();

        // 2. Scan Mode
        $modes = [
            '0' => 'Full Scan (Functions + Exploits + Signatures) [Default]',
            '1' => 'Lite Mode (Recommended for WordPress, less false positives)',
            '2' => 'Signatures Only (Fastest, least false positives)',
            '3' => 'Exploits Only',
        ];
        $mode = CLI::choice('Select scan mode:', $modes);

        switch ($mode) {
            case '1':
                Scanner::enableLiteMode();
                break;
            case '2':
                Scanner::setOnlySignaturesMode();
                break;
            case '3':
                Scanner::setOnlyExploitsMode();
                break;
                // Default 0 does nothing (Full scan)
        }
        CLI::newLine();

        // 3. Actions on detection
        $actions = [
            '0' => 'Report Only (Do not modify files) [Default]',
            '1' => 'Interactive (Ask for each detection)',
            '2' => 'Auto Quarantine',
            '3' => 'Auto Delete (Dangerous!)',
        ];
        $action = CLI::choice('Select action on detection:', $actions);

        switch ($action) {
            case '0':
                Scanner::setReportMode(true);
                break;
            case '1':
                // Default behavior is interactive if report mode is false
                Scanner::setReportMode(false);
                break;
            case '2':
                Scanner::setAutoQuarantine();
                break;
            case '3':
                Scanner::setAutoDelete();
                break;
        }

        // 4. Report Format (only if report mode or generally useful)
        if (Scanner::isReportMode()) {
            CLI::newLine();
            $formats = [
                '0' => 'HTML [Default]',
                '1' => 'Text',
            ];
            $format = CLI::choice('Select report format:', $formats);
            if ($format === '1') {
                Scanner::setReportFormat('txt');
            }
        }

        CLI::newLine();
        CLI::writeLine('Configuration completed! Starting scan...', 1, 'cyan');
        CLI::newLine();
    }
}
