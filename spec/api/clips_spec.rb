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
require'date'

describe "API::Clips" do
	
	describe "POST /clips" do
		
		before(:all) do
			@translation_request = OBSFactory.translation_request
		end
		
		it "Create via JSON" do
			url = "#{ROOT_URL}clips.json"
			request = RestClient.post url, :translation_request_token => @translation_request.token, :video_file => '1/the_compassionate_father_1.mp4'
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been submitted')
			response['vts']['clips'][0]['status'].should_not be_nil
			response['vts']['clips'][0]['status'].downcase.should eq('pending')
		end
		
		it "Create via XML" do
			url = "#{ROOT_URL}clips.xml"
			request = RestClient.post url, :translation_request_token => @translation_request.token, :video_file => '1/the_compassionate_father_1.mp4'
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been submitted')
			status = response.css("vts clips status").first.text
			status.should_not be_nil
			status.downcase.should eq('pending')
		end
		
	end
	
end