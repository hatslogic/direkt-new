<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Context;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use HatslogicPageOptimisation\HatslogicPageOptimisation;
use HatslogicPageOptimisation\Service\NodeFinder;
use HatslogicPageOptimisation\Service\ConfigService;

#[Route(defaults: ['_routeScope' => ['api']])]
class GtmController extends AbstractController
{

    public function __construct(
        private ConfigService $configService,
        private NodeFinder $nodeFinder
    ) {}
    #[Route(path: '/api/_action/hatslogic/convert/gtm-script', name: 'api.action.hatslogic.convert.gtm.script', methods: ['GET'])]
    public function optimizeGtm(Context $context): Response
    {
        try {
           
            $crtPath = HatslogicPageOptimisation::getPluginDir() . '/Partytown';
            $packageJsonPath = __DIR__ . '/../../Partytown/package.json';
            $nodeModulesPath = __DIR__ . '/../../Partytown';

            // Check if package.json exists
            if (!file_exists($packageJsonPath)) {
                throw new \Exception('package.json file not found.');
            }
            $nodeNpmPath = $this->nodeFinder->findNodeAndNpm();
            $npmPathConfig = $this->configService->npmPath();
            $nodePathConfig = $this->configService->nodePath();
            if (!$npmPathConfig) {
                //$npmpath = $nodeNpmPath['npm']['path'];
                $npmPath = "/opt/plesk/node/18/bin/npm";
                $nodePath = "/opt/plesk/node/18/bin";
                //$command = "$npmpath -v";
            } else {
                $nodePath = dirname(dirname($npmPathConfig)).'/bin';
                $npmPath = $nodePath . '/npm';
            }
            // Run npm install if node_modules directory is missing
            if (!file_exists($nodeModulesPath . '/node_modules')) {
            
                 $command = "PATH=$nodePath:\$PATH NODE_PATH=$nodePath/lib/node_modules $npmPath install --prefix $nodeModulesPath";

                $process = Process::fromShellCommandline($command);
                $process->setTimeout(300); // Set a timeout if needed

                try {
                    $process->mustRun();
                    echo $process->getOutput();
                } catch (ProcessFailedException $exception) {
                    echo 'Command failed: ', $exception->getMessage();
                }
            }
            if (!file_exists($nodeModulesPath . '/public/~partytown')) {
                $command1 = "PATH=$nodePath:\$PATH NODE_PATH=$nodePath/lib/node_modules $npmPath run build --prefix $nodeModulesPath";
                $process = Process::fromShellCommandline($command1);
                $process->setTimeout(500); // Set a timeout if needed
    
                try {
                    $process->mustRun();
                    echo $process->getOutput();
                } catch (ProcessFailedException $exception) {
                    echo 'Command failed: ', $exception->getMessage();
                }
                
            }
            if (file_exists($nodeModulesPath . '/public/~partytown')) {
                $checkExistingPath = $this->getProjectDir().'/public/~partytown';
                if (!file_exists($checkExistingPath)) {
                    rename($nodeModulesPath . '/public/~partytown', $checkExistingPath);
                }
            }
            return new JsonResponse([
                'success' => true,
                'message' => 'Generated optimization files successfully.',
                'output' => [],
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function getProjectDir(): string
    {
		 $currentDir = __DIR__;
			while (!file_exists($currentDir . '/vendor/shopware')) {
				// Move one level up
				$parentDir = dirname($currentDir);

				// If we've reached the top-level directory, break to avoid infinite loop
				if ($parentDir === $currentDir) {
					throw new \Exception("Project root directory not found (composer.json is missing).");
				}

				$currentDir = $parentDir;
			}

		return $currentDir;
    }
}
