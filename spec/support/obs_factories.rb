require "digest/sha1"
require 'date'

class TranslationRequest < ActiveRecord::Base
end

class OBSFactories
	
	# create a translation request
	# 
	def translation_request(options = {})
		attributes = {token: token, expires_at: (Date.today + 1), modified: Date.today, created: Date.today}
		attributes.merge!(options)
		TranslationRequest.create(attributes)
	end
	
	private
		def token
			Digest::SHA1.hexdigest(Time.now.to_s + rand(12341234).to_s)[1..25]
		end
	
end