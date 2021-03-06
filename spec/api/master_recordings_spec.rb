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
			@clip1 = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'COMPLETE'
															})
		end
		
		it "Create and respond with JSON" do
			url = "#{ROOT_URL}master_recordings.json"
			request = RestClient.post url, 
				:translation_request_token 	=> @translation_request.token, 
				:title 					  					=> 'The Compassionate Father',
				:language 			  					=> 'Greek',                   
				:final_filename   					=> 'gr_compassionate_father'  
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been submitted')
			response['vts']['master_recording']['status'].should_not be_nil
			response['vts']['master_recording']['translation_request_id'].should_not be_nil
			response['vts']['master_recording']['title'].should eq('The Compassionate Father')
			response['vts']['master_recording']['language'].should eq('Greek')
			response['vts']['master_recording']['final_filename'].should eq('gr_compassionate_father')
		end
		
		it "Create and respond with XML" do
			url = "#{ROOT_URL}master_recordings.xml"
			request = RestClient.post url, 
				:translation_request_token 	=> @translation_request.token, 
				:title 											=> 'The Feeding of 500',
				:language 									=> 'Spanish',
				:final_filename 						=> 'sp_feeding_500'
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been submitted')
			status = response.css("vts master_recording status").first.text
			status.should_not be_nil
			response.css("vts master_recording translation_request_id").text.should_not be_nil
			response.css("vts master_recording title").text.should eq('The Feeding of 500')
			response.css("vts master_recording language").text.should eq('Spanish')
			response.css("vts master_recording final_filename").text.should eq('sp_feeding_500')
		end
		
		it "should not let you add the translation_request_id" do
			new_translation_request_id = '383838383'
			url = "#{ROOT_URL}master_recordings.json"
			request = RestClient.post url,{ 
					:title 											=> 'The Feeding of 500',
					:language 									=> 'Spanish',
					:translation_request_id 		=> new_translation_request_id,
					:translation_request_token 	=> @translation_request.token,
					:final_filename 						=> 'sp_feeding_500'
				}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['master_recording']['translation_request_id'].should eq("#{@translation_request.id}")
			response['vts']['master_recording']['translation_request_id'].should_not eq("#{new_translation_request_id}")
		end
		
		describe "Should return valid errors" do
			
			it "Requires a title" do
				url = "#{ROOT_URL}master_recordings.json"
				begin
					request = RestClient.post url, 
						:translation_request_token 	=> @translation_request.token, 
						:title 											=> '',
						:language 									=> 'Greek',
						:final_filename 						=> 'gr_feeding_500'
					puts "      Requires a title - errored incorrectly"
				rescue => e
					e.response.code.should eq(400)
					response = JSON.parse(e.response)
					response['vts']['status'].should_not be_empty
					response['vts']['status'].should match('error')
					response['vts']['message'].should_not be_empty
					response['vts']['message'].downcase.should match('missing required attributes')
					response['vts']['details'].downcase.should match('supply a valid title')
					puts "      Requires a title - errored correctly"
				end
			end
			
			it "Requires a language" do
				url = "#{ROOT_URL}master_recordings.json"
				begin
					request = RestClient.post url, 
						:translation_request_token 	=> @translation_request.token, 
						:title 											=> 'My great master recording',
						:language 									=> '',
						:final_filename 						=> 'sp_feeding_500'
					puts "      Requires a language - errored incorrectly"
				rescue => e
					e.response.code.should eq(400)
					response = JSON.parse(e.response)
					response['vts']['status'].should_not be_empty
					response['vts']['status'].should match('error')
					response['vts']['message'].should_not be_empty
					response['vts']['message'].downcase.should match('missing required attributes')
					response['vts']['details'].downcase.should match('supply a valid language')
					puts "      Requires a language - errored correctly"
				end
			end
			
			it "Requires a final_filename" do
				url = "#{ROOT_URL}master_recordings.json"
				begin
					request = RestClient.post url, 
						:translation_request_token 	=> @translation_request.token, 
						:title 											=> 'My great master recording',
						:language 									=> 'greek',
						:final_filename 						=> ''
					puts "      Requires a final_filename - errored incorrectly"
				rescue => e
					e.response.code.should eq(400)
					response = JSON.parse(e.response)
					response['vts']['status'].should_not be_empty
					response['vts']['status'].should match('error')
					response['vts']['message'].should_not be_empty
					response['vts']['message'].downcase.should match('missing required attributes')
					response['vts']['details'].downcase.should match('supply a valid final filename')
					puts "      Requires a final_filename - errored correctly"
				end
			end
			
		end
		
	end
	
	describe "GET /master_recordings/id" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@clip1 = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'COMPLETE'
															})
			@master_recording = OBSFactory.master_recording({:translation_request_id => @translation_request.id})
		end
		
		it "Read and respond with JSON" do
			url = "#{ROOT_URL}master_recordings/#{@master_recording['id']}.json"
			request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should be_empty
			response['vts']['master_recording']['id'].should eq("#{@master_recording['id']}")
			response['vts']['master_recording']['title'].should eq("#{@master_recording['title']}")
			response['vts']['master_recording']['language'].should eq("#{@master_recording['language']}")
			response['vts']['master_recording']['status'].should eq("#{@master_recording['status']}")
			response['vts']['master_recording']['final_filename'].should eq("#{@master_recording['final_filename']}")
		end
		
		it "Read and respond with XML" do
			url = "#{ROOT_URL}master_recordings/#{@master_recording['id']}.xml"
			request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should be_empty
			response.css("vts master_recording id").first.text.should eq("#{@master_recording['id']}")
			response.css("vts master_recording title").first.text.should eq("#{@master_recording['title']}")
			response.css("vts master_recording language").first.text.should eq("#{@master_recording['language']}")
			response.css("vts master_recording status").first.text.should eq("#{@master_recording['status']}")
			response.css("vts master_recording final_filename").first.text.should eq("#{@master_recording['final_filename']}")
		end
		
		it "404 Error (resource missing)" do
			url = "#{ROOT_URL}master_recordings/9999999999999999999999.json"
			begin
				request = RestClient.get url, {:params => {:translation_request_token => @translation_request.token}}
				puts "    404 Error (resource missing) - errored incorrectly"
			rescue => e
				e.response.code.should eq(404)
				response = JSON.parse(e.response)
				response['vts']['status'].should_not be_empty
				response['vts']['status'].should match('error')
				response['vts']['message'].should_not be_empty
				response['vts']['message'].downcase.should match('invalid resource')
				puts "    404 Error (resource missing) - errored correctly"
			end
		end
		
	end
	
	describe "PUT /master_recordings/id" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@clip1 = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'COMPLETE'
															})
			@master_recording = OBSFactory.master_recording({:translation_request_id => @translation_request.id})
		end
		
		it "Update and respond with JSON" do
			expected_title = 'Zombie Video'
			expected_lang = 'Dutch'
			expected_final_filename = 'dc_zombie_vid'
			url = "#{ROOT_URL}master_recordings/#{@master_recording['id']}.json"
			request = RestClient.post url, {
				:title 											=> expected_title,
				:language 									=> expected_lang,
				:translation_request_token 	=> @translation_request.token,
				'_method' 									=> 'PUT',
				:final_filename 						=> expected_final_filename
			}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been modified')
			response['vts']['master_recording']['title'].should eq("#{expected_title}")
			response['vts']['master_recording']['language'].should eq("#{expected_lang}")
			response['vts']['master_recording']['final_filename'].should eq("#{expected_final_filename}")
		end
		
		it "Update and respond with XML" do
			expected_title = 'Frankenstein Video'
			expected_lang = 'Cantonese'
			expected_final_filename = 'ct_frankie_vid'
			url = "#{ROOT_URL}master_recordings/#{@master_recording['id']}.xml"
			request = RestClient.post url, {
				:title 											=> expected_title,
				:language 									=> expected_lang,
				:translation_request_token 	=> @translation_request.token,
				'_method' 									=> 'PUT',
				:final_filename 						=> expected_final_filename
			}
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been modified')
			response.css("vts master_recording title").first.text.should eq("#{expected_title}")
			response.css("vts master_recording language").first.text.should eq("#{expected_lang}")
			response.css("vts master_recording final_filename").first.text.should eq("#{expected_final_filename}")
		end
		
		it "404 Error (resource missing)" do
			url = "#{ROOT_URL}master_recordings/9999999999999999999999.json"
			begin
				request = RestClient.post url, {
					:title 											=> 'My Title',
					:language 									=> 'A Lang',
					:translation_request_token 	=> @translation_request.token,
					'_method' 									=> 'PUT',
					:final_filename 						=> 'sp_feeding_500'
				}
				puts "    404 Error (resource missing) - errored incorrectly"
			rescue => e
				e.response.code.should eq(404)
				response = JSON.parse(e.response)
				response['vts']['status'].should_not be_empty
				response['vts']['status'].should match('error')
				response['vts']['message'].should_not be_empty
				response['vts']['message'].downcase.should match('invalid resource')
				puts "    404 Error (resource missing) - errored correctly"
			end
		end
		
		it "should set status to pending if modified" do
			master_recording = OBSFactory.master_recording({:translation_request_id => @translation_request.id, :status => 'COMPLETE'})
			url = "#{ROOT_URL}master_recordings/#{master_recording.id}.json"
			request = RestClient.post url, {
				:title 											=> 'My Title',
				:language 									=> 'A Lang',
				:translation_request_token 	=> @translation_request.token,
				'_method' 									=> 'PUT',
				:final_filename 						=> 'sp_feeding_500'
			}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['master_recording']['status'].should_not be_nil
			response['vts']['master_recording']['status'].downcase.should eq('pending')
		end
		
		it "should not let you change the translation_request_id" do
			new_translation_request_id = '45637261188'
			url = "#{ROOT_URL}master_recordings/#{@master_recording['id']}.json"
			request = RestClient.post url, {
				:title 											=> 'Zombie Video',
				:language 									=> 'Dutch',
				:translation_request_token 	=> @translation_request.token,
				:translation_request_id 	=> new_translation_request_id,
				'_method' 									=> 'PUT',
				:final_filename 						=> 'sp_feeding_500'
			}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['master_recording']['translation_request_id'].should eq("#{@translation_request.id}")
			response['vts']['master_recording']['translation_request_id'].should_not eq("#{new_translation_request_id}")
		end
		
	end
	
	describe "DELETE /master_recordings/id" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
			@clip1 = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'COMPLETE'
															})
			@master_recording = OBSFactory.master_recording({:translation_request_id => @translation_request.id})
		end
		
		it "Delete and respond with JSON" do
			url = "#{ROOT_URL}master_recordings/#{@master_recording.id}.json"
			request = RestClient.post url, {:translation_request_token => @translation_request.token, '_method' => 'DELETE'}
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been deleted')
			response['vts']['master_recording'].should be_empty
			OBSFactory.master_recording_exists?(@master_recording.id).should be_false
		end
		
		it "Delete and respond with XML" do
			url = "#{ROOT_URL}master_recordings/#{@master_recording.id}.xml"
			request = RestClient.post url, {:translation_request_token => @translation_request.token, '_method' => 'DELETE'}
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").first.text.should eq('success')
			response.css("vts message").text.should match('has been deleted')
			response.css("vts master_recording").text.should be_empty
			OBSFactory.master_recording_exists?(@master_recording.id).should be_false
		end
		
		it "404 Error (resource missing)" do
			url = "#{ROOT_URL}master_recordings/9999999999999999999999.json"
			begin
				request = RestClient.post url, {:translation_request_token => @translation_request.token, '_method' => 'DELETE'}
				puts "    404 Error (resource missing) - errored incorrectly"
			rescue => e
				e.response.code.should eq(404)
				response = JSON.parse(e.response)
				response['vts']['status'].should_not be_empty
				response['vts']['status'].should match('error')
				response['vts']['message'].should_not be_empty
				response['vts']['message'].downcase.should match('invalid resource')
				puts "    404 Error (resource missing) - errored correctly"
			end
		end
	end
	
	describe "must have valid Translation Request ID" do

		it "should error if missing" do
			url = "#{ROOT_URL}master_recordings.json"
			begin
			  request = RestClient.post url, 
					:translation_request_token 	=> '', 
					:title 											=> 'The Compassionate Father',
					:language 									=> 'Greek',
					:final_filename 						=> 'sp_feeding_500'
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
					:translation_request_token 	=> translation_request.token, 
					:title 											=> 'The Compassionate Father',
					:language 									=> 'Greek',
					:final_filename 						=> 'sp_feeding_500'
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
					:translation_request_token 	=> 'whyohwhy', 
					:title 											=> 'The Compassionate Father',
					:language 									=> 'Greek',
					:final_filename 						=> 'sp_feeding_500'
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
	
	describe "must have completed clips" do
		
		before(:each) do
			@translation_request = OBSFactory.translation_request
		end
		
		it "requires at least 1 completed clip" do
			begin
				url = "#{ROOT_URL}master_recordings.json"
				request = RestClient.post url, 
					:translation_request_token 	=> @translation_request.token, 
					:title 											=> 'The Compassionate Man',
					:language 									=> 'Portugese',
					:final_filename 						=> 'sp_feeding_500'
				puts request
				puts "    requires at least 1 completed clip - errored incorrectly"	
			rescue => e
				e.response.code.should eq(401)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('unauthorized')
				response['vts']['details'].downcase.should match('at least 1 clip')
				puts "    requires at least 1 completed clip - errored correctly"
			end
		end
		
		it "requires all clips to be completed" do
			clip1 = OBSFactory.clip({
																:translation_request_id		 	=> @translation_request.id, 
																:audio_file_location 				=> '/really/made/up/file.mp3', 
																:video_file_location 				=> 'unique/video/file.mp4',
																:status 										=> 'PENDING'
															})
			begin
				url = "#{ROOT_URL}master_recordings.json"
				request = RestClient.post url, 
					:translation_request_token 	=> @translation_request.token, 
					:title 											=> 'The Compassionate Man',
					:language 									=> 'Portugese',
					:final_filename 						=> 'sp_feeding_500'
				puts request
				puts "    requires all clips to be completed - errored incorrectly"	
			rescue => e
				e.response.code.should eq(401)
				response = JSON.parse(e.response)
				response['vts']['status'].should eq('error')
				response['vts']['message'].downcase.should match('unauthorized')
				response['vts']['details'].downcase.should match('all clips need a status of complete')
				puts "    requires all clips to be completed - errored correctly"
			end
		end
		
	end
	
end