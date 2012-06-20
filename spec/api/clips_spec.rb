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
require 'digest/md5'

describe "API::Clips" do
	
	describe "POST /clips" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@expected_file = '';
		end
		
		after(:each) do
			File.delete(@expected_file) unless @expected_file.empty?
		end
		
		it "Create and respond with JSON" do
			url = "#{ROOT_URL}clips.json"
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:video_file_location => '1/the_compassionate_father_1.mp4',
				:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
				:multipart => true
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been submitted')
			response['vts']['clips'][0]['status'].should_not be_nil
			response['vts']['clips'][0]['status'].downcase.should eq('pending')
			response['vts']['clips'][0]['audio_file_location'].should_not be_nil
			response['vts']['clips'][0]['audio_file_location'].should_not be_empty
			response['vts']['clips'][0]['video_file_location'].should_not be_nil
			response['vts']['clips'][0]['video_file_location'].should_not be_empty
			@expected_file = File.join(WEBROOT_DIRECTORY, response['vts']['clips'][0]['audio_file_location'])
			File.exists?(@expected_file).should be_true
		end
		
		it "Create and respond with XML" do
			url = "#{ROOT_URL}clips.xml"
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:video_file_location => '1/the_compassionate_father_1.mp4',
				:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_2.mp3'), 'rb'), 
				:multipart => true
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been submitted')
			status = response.css("vts clips status").first.text
			status.should_not be_nil
			status.downcase.should eq('pending')
			audio_file_url = response.css("vts clips audio_file_location").text
			audio_file_url.should_not be_nil
			audio_file_url.should_not be_empty
			video_file_url = response.css("vts clips video_file_location").text
			video_file_url.should_not be_nil
			video_file_url.should_not be_empty
			@expected_file = File.join(WEBROOT_DIRECTORY, audio_file_url)
			File.exists?(@expected_file).should be_true
		end
		
		it "requires an audio file" do
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => @translation_request.token, 
					:video_file_location => '1/the_compassionate_father_1.mp4',
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
		
		it "requires a mp3 audio file" do
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => @translation_request.token, 
					:video_file_location => '1/the_compassionate_father_1.mp4',
					:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_2.mp4'), 'rb'),
					:multipart => true
				puts "    requires a mp3 audio file - errored incorrectly"
			rescue => e
				e.response.code.should eq(400)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('missing required attributes')
				puts "    requires a mp3 audio file - errored correctly"
			end
		end
	end
	
	describe "PUT clips/id" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@clip = OBSFactory.clip({:translation_request_id => @translation_request.id, :audio_file_location => '/made/up/file.mp3'})
			@expected_file = '';
		end
		
		after(:each) do
			File.delete(@expected_file) unless @expected_file.empty?
		end
		
		it "Modify and respond with JSON" do
			url = "#{ROOT_URL}clips/#{@clip.id}.json"
			expected_video_file_location = 'my/unique_file_url.mp4'
			# We have a max filename size of 30 characters
			#
			expected_audio_file_name = "#{Digest::MD5.hexdigest('23_1.mp3')}"[0,30]
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:video_file_location => expected_video_file_location,
				:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
				:multipart => true,
				'_method' => 'PUT'
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been modified')
			response['vts']['clips'][0]['status'].should_not be_nil
			response['vts']['clips'][0]['status'].downcase.should eq('pending')
			response['vts']['clips'][0]['audio_file_location'].should_not eq('/made/up/file.mp3')
			response['vts']['clips'][0]['audio_file_location'].should eq("/files/clips/#{expected_audio_file_name}.mp3")
			response['vts']['clips'][0]['video_file_location'].should eq(expected_video_file_location)
			@expected_file = File.join(WEBROOT_DIRECTORY, response['vts']['clips'][0]['audio_file_location'])
			File.exists?(@expected_file).should be_true
		end
		
		it "Modify and respond with XML" do
			url = "#{ROOT_URL}clips/#{@clip.id}.xml"
			expected_video_file_location = 'my/unique_file_url.mp4'
			# We have a max filename size of 30 characters
			#
			expected_audio_file_name = "#{Digest::MD5.hexdigest('23_2.mp3')}"[0,30]
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:video_file_location => expected_video_file_location,
				:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_2.mp3'), 'rb'), 
				:multipart => true,
				'_method' => 'PUT'
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been modified')
			status = response.css("vts clips status").first.text
			status.should_not be_nil
			status.downcase.should eq('pending')
			audio_file_url = response.css("vts clips audio_file_location").text
			audio_file_url.should_not eq('/made/up/file.mp3')
			audio_file_url.should eq("/files/clips/#{expected_audio_file_name}.mp3")
			video_file_url = response.css("vts clips video_file_location").text
			video_file_url.should eq(expected_video_file_location)
			@expected_file = File.join(WEBROOT_DIRECTORY, audio_file_url)
			File.exists?(@expected_file).should be_true
		end
		
		it "should set status to pending if modified" do
			clip = OBSFactory.clip({:translation_request_id => @translation_request.id, :status => 'COMPLETE'})
			url = "#{ROOT_URL}clips/#{clip.id}.json"
			# We have a max filename size of 30 characters
			#
			request = RestClient.post url, 
				:translation_request_token => @translation_request.token, 
				:video_file_location => '',
				:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
				:multipart => true,
				'_method' => 'PUT'
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['clips'][0]['status'].should_not be_nil
			response['vts']['clips'][0]['status'].downcase.should eq('pending')
		end
		
	end
	
	describe "GET /clips/id" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@clip = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'PROCESSING'
															})
		end
		
		it "READ and respond with JSON" do
			url = "#{ROOT_URL}clips/#{@clip.id}.json"
			request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should be_empty
			response['vts']['clips'][0]['id'].should eq("#{@clip['id']}")
			response['vts']['clips'][0]['audio_file_location'].should eq(@clip['audio_file_location'])
			response['vts']['clips'][0]['video_file_location'].should eq("#{@clip['video_file_location']}")
			response['vts']['clips'][0]['status'].should eq("#{@clip['status']}")
		end
		
		it "READ and respond with XML" do
			url = "#{ROOT_URL}clips/#{@clip.id}.xml"
			request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should be_empty
			status = response.css("vts clips status").first.text
			status.should_not be_nil
			status.should eq("#{@clip['status']}")
			audio_file_url = response.css("vts clips audio_file_location").text
			audio_file_url.should eq(@clip['audio_file_location'])
			video_file_url = response.css("vts clips video_file_location").text
			video_file_url.should eq("#{@clip['video_file_location']}")
		end
		
		it "should throw 404 if incorrect resource" do
			url = "#{ROOT_URL}clips/9999999999999999999999.json"
			begin
			  request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
				puts "    should throw 404 if incorrect resource - errored incorrectly"
			rescue => e
			  e.response.code.should eq(404)
				response = JSON.parse(e.response)
				response['vts']['status'].should_not be_empty
				response['vts']['status'].should match('error')
				response['vts']['message'].should_not be_empty
				response['vts']['message'].downcase.should match('invalid resource')
				puts "    should throw 404 if incorrect resource - errored correctly"
			end
		end
	
	end
	
	describe "GET /clips" do
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@clip1 = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'PROCESSING'
															})
			@clip2 = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'PROCESSING'
															})
			@not_related_translation_request = OBSFactory.translation_request											
			@not_related_clip = OBSFactory.clip({
																:translation_request_id		 	=> @not_related_translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'PROCESSING'
															})
			@expected_clips = Array.new
			@expected_clips << @clip1.id
			@expected_clips << @clip2.id
		end
		
		it "READ all clips for translation_request and respond with JSON" do
			url = "#{ROOT_URL}clips.json"
			request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should be_empty
			response['vts']['clips'].length.should eq(2)
			response['vts']['clips'].each do |clip|
				clip['translation_request_id'].should eq("#{@translation_request.id}")
				@expected_clips.include?(clip['id'].to_i).should be_true
			end
		end
		
		it "READ all clips for translation_request and respond with XML" do
			url = "#{ROOT_URL}clips.xml"
			request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should be_empty
			clips = response.css("vts clips")
			clips.length.should eq(2)
			clips.each do |clip|
				clip.children.xpath("//translation_request_id").first.text.should eq("#{@translation_request.id}")
				@expected_clips.include?(clip.children.xpath("//id").first.text.to_i).should be_true
			end
		end
		
	end
	
	describe "DELETE clips/id" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@clip = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'PROCESSING'
															})
		end
		
		it "Delete and respond with JSON" do
			url = "#{ROOT_URL}clips/#{@clip.id}.json"
			request = RestClient.post url, {:translation_request_token => @translation_request.token, '_method' => 'DELETE'}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been deleted')
			response['vts']['clips'].should be_empty
		end
		
		it "Delete and respond with XML" do
			url = "#{ROOT_URL}clips/#{@clip.id}.xml"
			request = RestClient.post url, {:translation_request_token => @translation_request.token, '_method' => 'DELETE'}
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been deleted')
			response.css("vts clips").children.length.should eq(0)
		end
		
	end
	
	describe "must have valid Translation Request ID" do

		it "should error if missing" do
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => '', 
					:video_file_location => '1/the_compassionate_father_1.mp4',
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
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => translation_request.token, 
					:video_file_location => '1/the_compassionate_father_1.mp4',
					:audio_file => File.new(File.join(SPEC_DIRECTORY,'files','audio', '23_1.mp3'), 'rb'), 
					:multipart => true
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
			url = "#{ROOT_URL}clips.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token => "really", 
					:video_file_location => '1/the_compassionate_father_1.mp4',
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