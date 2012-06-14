class TranslationRequestsFixture
	
	# create a translation request
	# 
	def create
		url = "#{ROOT_URL}translation_requests.json"
		request = RestClient.post url, {}.to_json, :content_type => :json, :accept => :json
		response = JSON.parse(request)
		response['vts']['translation_requests'][0]
	end
	
end