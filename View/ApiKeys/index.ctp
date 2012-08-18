<?php
/**
 * This file is part of Video Translator Service Website Example.
 * 
 * Video Translator Service Website Example is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Video Translator Service Website Example is distributed in the hope that it will be useful,
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
?>
<h1>API Keys</h1>
<div id="api-keys list-api-keys">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>Application</th>
				<th>Key</th>
				<th></th>
			</tr>
		</thead>
	  <tbody>
	    <?php foreach($apiKeys as $apiKey): ?>
				<tr>
			    <td >
						<?php echo $apiKey['ApiKey']['app_resource']; ?>
					</td>
					<td >
						<?php echo $apiKey['ApiKey']['hash_key']; ?>
					</td>
			    <td class="actions">
						<?php 
							echo $this->Form->postLink('<i class="icon-trash"></i> ' . __('Delete'), array('controller'	=>	'api_keys', 'action'	=>	'delete', $apiKey['ApiKey']['id']), array('escape'	=>	false), sprintf(__('Are you sure you want to delete the API Key %s?'), $apiKey['ApiKey']['app_resource'])); 
						?>
					</td>
			  </tr>
			<?php endforeach; ?>
			<?php if(empty($apiKeys)): ?>
				<tr>
					<td colspan="3">No API Keys</td>
				</tr>
			<?php endif; ?>
	  </tbody>
	</table>
</div>