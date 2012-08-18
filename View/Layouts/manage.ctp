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
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo __('Video Translator Service API'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <?php echo $this->Html->css(array('bootstrap.min', 'bootstrap-responsive.min')); ?>
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="navbar">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/"><?php echo __('VTS API'); ?></a>
          <div class="nav-collapse">
            <ul class="nav pull-right">
              <li><?php echo $this->Html->link(__('Home'), '/'); ?></li>
							<li>
								<div>
									<?php echo $this->Html->link(__('Add an API Key'), array('controller' => 'api_keys', 'action' => 'add', 'admin'	=>	false), array('class' => 'btn btn-primary')); ?>
								</div>
							</li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <div class="container">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
    </div> <!-- /container -->
		<script type="text/javascript" charset="utf-8" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<?php echo $this->Html->script(array('bootstrap.min')); ?>
  </body>
</html>