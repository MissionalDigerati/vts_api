<?php
/**
 * This file is part of OBS Video Translator API.
 * 
 * OBS Video Translator API is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * OBS Video Translator API is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see 
 * <http://www.gnu.org/licenses/>.
 *
 * @author Johnathan Pulos <johnathan@missionaldigerati.org>
 * @copyright Copyright 2012 Missional Digerati
 * 
 */
/**
 * CakePHP hands off the process here, so it can continue its work uninterupted.  This file triggers the vts_processor.php in the scripts directory,
 * and silently logs the result in the tmp/logs/processor.log.  This file takes 2 arguments, similar to vts_processor.php.
 * SERVICE: CLIP or MASTER_RECORDING
 * RESOURCE_ID: The resources id
 *
 * @author Johnathan Pulos
 */
$service = (isset($argv[1])) ? strtoupper($argv[1]): 'CLIP';
$resourceId = (isset($argv[2])) ? ' ' . $argv[2]: '';
$appDirectory = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$cmd = "php scripts" . DIRECTORY_SEPARATOR . "vts_processor.php " . strtoupper($service) . $resourceId . " > tmp" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "processor.log 2>&1 & echo $!";
exec($cmd);
?>