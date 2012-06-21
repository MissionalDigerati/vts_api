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

describe "API::MasterRecordings" do
	
	describe "POST /master_recordings" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
		end
		
		it "Create and respond with JSON" do
			url = "#{ROOT_URL}master_recordings.json"
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:title => 'The Compassionate Father',
				:language => 'Greek'
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been submitted')
			response['vts']['master_recordings'][0]['status'].should_not be_nil
			response['vts']['master_recordings'][0]['status'].downcase.should eq('pending')
			response['vts']['master_recordings'][0]['title'].should eq('The Compassionate Father')
			response['vts']['master_recordings'][0]['language'].should eq('Greek')
		end
		
		it "Create and respond with XML" do
			url = "#{ROOT_URL}master_recordings.xml"
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:title => 'The Feeding of 500',
				:language => 'Spanish'
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been submitted')
			status = response.css("vts master_recordings status").first.text
			status.should_not be_nil
			status.downcase.should eq('pending')
			response.css("vts master_recordings title").text.should eq('The Feeding of 500')
			response.css("vts master_recordings language").text.should eq('Spanish')
		end
		
	end
	
	describe "must have valid Translation Request ID" do

		it "should error if missing" do
			url = "#{ROOT_URL}master_recordings.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => '', 
					:title => 'The Compassionate Father',
					:language => 'Greek'
				puts "    should error if missing - errored incorrectly"
			rescue => e
				e.response.code.should eq(401)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('unauthorized')
				puts "    should error if missing - errored correctly"
			end
		end
		
		it "should error if expired" do
			translation_request = OBSFactory.translation_request({expires_at: (Date.today - 4)})
			url = "#{ROOT_URL}master_recordings.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => translation_request.token, 
					:title => 'The Compassionate Father',
					:language => 'Greek'
				puts "    should error if expired - errored incorrectly"
			rescue => e
				e.response.code.should eq(401)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('unauthorized')
				puts "    should error if expired - errored correctly"
			end
		end
		
		it "should error if it does not exist" do
			url = "#{ROOT_URL}master_recordings.json"
			begin
				request = RestClient.post url, 
					:translation_request_token => 'whyohwhy', 
					:title => 'The Compassionate Father',
					:language => 'Greek'
				puts "    should error if it does not exist - errored incorrectly"
			rescue => e
				e.response.code.should eq(404)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('invalid resource')
				puts "    should error if it does not exist - errored correctly"
			end
		end
		
	end
	
end