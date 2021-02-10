<?php
declare(strict_types=1);
namespace sw;
use pocketmine\level\Level;
/**
 * Class MapBackup
 * @package sw
 */
class MapBackup{

    /** @var Arena $plugin */
    private $plugin;
    private $path;

    /**
     * MapReset constructor.
     * @param Arena $plugin
     */
    public function __construct(SkyWar $plugin){
        $this->plugin = $plugin;
        $this->path = $plugin->getDataPath();
    }

    /**
     * @param Level $level
     */
    public function saveMap(Level $level){
        $level->save(true);

        $levelPath = $this->path . "worlds" . DIRECTORY_SEPARATOR . $level->getFolderName();
        $zipPath = $this->path . "saves" . DIRECTORY_SEPARATOR . $level->getFolderName() . ".zip";

        $zip = new \ZipArchive();

        if(is_file($zipPath)) unlink($zipPath);

        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(realpath($levelPath)), \RecursiveIteratorIterator::LEAVES_ONLY);

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if($file->isFile()) {
                $filePath = $file->getPath() . DIRECTORY_SEPARATOR . $file->getBasename();
                $localPath = substr($filePath, strlen($this->plugin->getServer()->getDataPath() . "worlds"));
                $zip->addFile($filePath, $localPath);
            }
        }

        $zip->close();
    }

    /**
     * @param string $folderName
     * @param bool $justSave
     *
     * @return Level|null
     */
    public function loadMap(string $folderName, bool $justSave = false): ?Level {
        if(!$this->plugin->getServer()->isLevelGenerated($folderName)) {
            return null;
        }

        if($this->plugin->getServer()->isLevelLoaded($folderName)) {
            $this->plugin->getServer()->getLevelByName($folderName)->unload(true);
        }

        $zipPath = $this->plugin->getDataFolder() . "saves" . DIRECTORY_SEPARATOR . $folderName . ".zip";

        if(!file_exists($zipPath)) {
            $this->plugin->getServer()->getLogger()->error("Could not reload map ($folderName). File wasn't found, try save level in setup mode.");
            return null;
        }

        $zipArchive = new \ZipArchive();
        $zipArchive->open($zipPath);
        $zipArchive->extractTo($this->plugin->getServer()->getDataPath() . "worlds");
        $zipArchive->close();

        if($justSave) {
            return null;
        }

        $this->plugin->getServer()->loadLevel($folderName);
        return $this->plugin->getServer()->getLevelByName($folderName);
    }
}