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
require "active_record"
require "yaml"

# Setup Active Record
#
db_config = YAML::load(File.open(File.join(File.dirname(__FILE__),'config','database.yml')))['development']
ActiveRecord::Base.establish_connection(db_config)

# Require the support directory
#
support_dir = File.expand_path("../support", __FILE__)
Dir["#{support_dir}/**/*.rb"].each {|f| require f}

# Set the local vhost
#
ROOT_URL = 'http://api.obs.local/'
cleaner = DatabaseCleaner.new
table_names = ['translation_requests', 'clips']
OBSFactory = OBSFactories.new
SPEC_DIRECTORY = File.dirname(__FILE__)
WEBROOT_DIRECTORY = File.expand_path("../../webroot", __FILE__)

# RSpec configuration
RSpec.configure do |config|
	
	config.before(:suite) do
		cleaner.truncate_tables(table_names)
  end

  config.before(:each) do
  end

  config.after(:each) do
  end

	config.after(:suite) do
		cleaner.truncate_tables(table_names)
  end

end