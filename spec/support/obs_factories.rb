require "digest/sha1"
require 'date'

class TranslationRequest < ActiveRecord::Base
end
class Clip < ActiveRecord::Base
end

class OBSFactories
	
	# create a translation request
	# 
	def translation_request(options = {})
		attributes = {:token => token, :expires_at => (Date.today + 1), :modified => Date.today, :created => Date.today}
		attributes.merge!(options)
		TranslationRequest.create!(attributes)
	end
	
	# create a clip
	#
	def clip(options = {})
		translation_request = self.translation_request
		attributes = {:translation_request_id => translation_request.id, :audio_file_location => 'files/clips/fake_file.mp3', :video_file_location => '1/the_compassionate_father_1.mp4', :modified => Date.today, :created => Date.today}
		attributes.merge!(options)
		Clip.create!(attributes)
	end
	
	private
		def token
			Digest::SHA1.hexdigest(Time.now.to_s + rand(12341234).to_s)[1..25]
		end
	
end