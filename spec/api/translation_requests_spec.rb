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
	
	describe "GET /translation_request/id" do
		
		before(:all) do
			# create a translation request
			#
			translation_fixture = TranslationRequestsFixture.new
			@translation_request = translation_fixture.create
		end
		
		it "Read via JSON" do
			url = "#{ROOT_URL}translation_requests/#{@translation_request['id']}.json"
			request = RestClient.get url, :content_type => :json, :accept => :json
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should be_empty
			response['vts']['translation_requests'][0]['id'].should eq(@translation_request['id'])
			response['vts']['translation_requests'][0]['token'].should eq(@translation_request['token'])
			response['vts']['translation_requests'][0]['expires_at'].should eq(@translation_request['expires_at'])
		end
	
		it "Read via XML" do
			url = "#{ROOT_URL}translation_requests/#{@translation_request['id']}.xml"
			request = RestClient.get url, :content_type => :xml, :accept => :xml
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").text.should eq('success')
			response.css("vts message").text.should be_empty
			id = response.css("vts translation_requests id").first.text
			id.should eq(@translation_request['id'])
			token = response.css("vts translation_requests token").first.text
			token.should eq(@translation_request['token'])
			expires = response.css("vts translation_requests expires_at").first.text
			expires.should eq(@translation_request['expires_at'])
		end
		
		it "404 Error (resource missing) via JSON" do
			url = "#{ROOT_URL}translation_requests/9999999999999.json"
			begin
			  request = RestClient.get url, :content_type => :json, :accept => :json
			rescue => e
			  e.response.code.should eq(404)
				response = JSON.parse(e.response)
				response['vts']['status'].should_not be_empty
				response['vts']['status'].should match('error')
				response['vts']['message'].should_not be_empty
				response['vts']['message'].downcase.should match('invalid resource')
			end
		end
		
		it "404 Error (resource missing) via XML" do
			url = "#{ROOT_URL}translation_requests/9999999999999.xml"
			begin
			  request = RestClient.get url, :content_type => :xml, :accept => :xml
			rescue => e
			  e.response.code.should eq(404)
				response = Nokogiri::XML(e.response)
				response.css("vts status").text.should_not be_empty
				response.css("vts status").text.should match('error')
				response.css("vts message").text.should_not be_empty
				response.css("vts message").text.downcase.should match('invalid resource')
			end
		end
		
	end
	
	describe "POST /translation_request" do
		it "Create via JSON" do
			url = "#{ROOT_URL}translation_requests.json"
			request = RestClient.post url, {}.to_json, :content_type => :json, :accept => :json
			request.code.should eq(200)
			response = JSON.parse(request)
			response['vts']['status'].should eq('success')
			response['vts']['message'].should match('has been created')
			response['vts']['translation_requests'][0]['token'].should_not be_nil
			response['vts']['translation_requests'][0]['token'].should_not be_empty
			response['vts']['translation_requests'][0]['expires_at'].should_not be_nil
			response['vts']['translation_requests'][0]['expires_at'].should_not be_empty
		end
	
		it "Create via XML" do
			url = "#{ROOT_URL}translation_requests.xml"
			request = RestClient.post url, "", :content_type => :xml, :accept => :xml
			request.code.should eq(200)
			response = Nokogiri::XML(request)
			response.css("vts status").text.should eq('success')
			response.css("vts message").text.should match('has been created')
			token = response.css("vts translation_requests token").first.text
			token.should_not be_nil
			token.should_not be_empty
			expires = response.css("vts translation_requests expires_at").first.text
			expires.should_not be_nil
			expires.should_not be_empty
		end
	end
	
end