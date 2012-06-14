#
 # This file is part of OBS Video Translator API.
 # 
 # OBS Video Translator API is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 # 
 # OBS Video Translator API is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 # 
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see 
 # <http://www.gnu.org/licenses/>.
 # @author Johnathan Pulos <johnathan@missionaldigerati.org>
 # @copyright Copyright 2012 Missional Digerati
#
require 'rubygems'
require 'json'
require 'rspec'
require 'rest_client'
require 'nokogiri'
# Require the support directory
#
support_dir = File.expand_path("../support", __FILE__)
Dir["#{support_dir}/**/*.rb"].each {|f| require f}

# Set the local vhost
#
ROOT_URL = 'http://api.obs.local/'

# RSpec configuration
RSpec.configure do |config|
end