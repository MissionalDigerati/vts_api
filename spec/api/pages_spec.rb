require 'spec_helper'

describe "requesting static pages" do
	
	it "should find the index page" do
		response = RestClient.get ROOT_URL
		response.code.should eq(200)
	end
	
end