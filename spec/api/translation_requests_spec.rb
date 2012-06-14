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
require 'spec_helper'

describe "API::TranslationRequests" do
	
	it "should create a new translation request with JSON request" do
		url = "#{ROOT_URL}translation_requests.json"
		request = RestClient.post url, {}.to_json, :content_type => :json, :accept => :json
		request.code.should eq(200)
		response = JSON.parse(request)
		response['status'].should eq('success')
		response['message'].should match('has been created')
		response['translation_request']['token'].should_not be_nil
		response['translation_request']['token'].should_not be_empty
		response['translation_request']['expires_at'].should_not be_nil
		response['translation_request']['expires_at'].should_not be_empty
	end
	
end