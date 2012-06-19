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
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
		end
		
		it "Create via JSON" do
			url = "#{ROOT_URL}clips.json"
			
			puts @audio_path
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:video_file => '1/the_compassionate_father_1.mp4',
				:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
				:multipart => true
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been submitted')
			response['vts']['clips'][0]['status'].should_not be_nil
			response['vts']['clips'][0]['status'].downcase.should eq('pending')
		end
		
		it "Create via XML" do
			url = "#{ROOT_URL}clips.xml"
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:video_file => '1/the_compassionate_father_1.mp4',
				:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
				:multipart => true
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been submitted')
			status = response.css("vts clips status").first.text
			status.should_not be_nil
			status.downcase.should eq('pending')
		end
		
		it "requires an audio file" do
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => @translation_request.token, 
					:video_file => '1/the_compassionate_father_1.mp4',
					:multipart => true
				puts "    requires an audio file - errored incorrectly"
			rescue => e
				e.response.code.should eq(400)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('missing required attributes')
				puts "    requires an audio file - errored correctly"
			end
		end
		
	end
	
	describe "must have valid Translation Request ID" do

		it "should error if missing" do
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => '', 
					:video_file => '1/the_compassionate_father_1.mp4',
					:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
					:multipart => true
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
			puts translation_request.inspect
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => translation_request.token, 
					:video_file => '1/the_compassionate_father_1.mp4',
					:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
					:multipart => true
				puts "    should error if expired - errored incorrectly"
				puts request
			rescue => e
				e.response.code.should eq(401)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('unauthorized')
				puts "    should error if expired - errored correctly"
			end
		end
		
		it "should error if it does not exist" do
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => "really", 
					:video_file => '1/the_compassionate_father_1.mp4',
					:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
					:multipart => true
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