require "digest/sha1"
require 'date'

class ApiKey < ActiveRecord::Base
end
class TranslationRequest < ActiveRecord::Base
end
class Clip < ActiveRecord::Base
end
class MasterRecording < ActiveRecord::Base
end

class OBSFactories
	
	# create a translation request
	# 
	def api_key(options = {})
		attributes = {:app_resource => 'test tool', :hash_key => token, :modified => Date.today, :created => Date.today}
		attributes.merge!(options)
		ApiKey.create!(attributes)
	end
	
	# check if the translation_request exists
	#
	def api_key_exists?(id)
		ApiKey.exists?(id)
	end
	
	# create a translation request
	# 
	def translation_request(options = {})
		attributes = {:token => token, :expires_at => (Date.today + 1), :modified => Date.today, :created => Date.today}
		attributes.merge!(options)
		TranslationRequest.create!(attributes)
	end
	
	# check if the translation_request exists
	#
	def translation_request_exists?(id)
		TranslationRequest.exists?(id)
	end
	
	# create a clip
	#
	def clip(options = {})
		translation_request = self.translation_request
		attributes = {:translation_request_id => translation_request.id, :audio_file_location => 'files/clips/fake_file.mp3', :video_file_location => '1/the_compassionate_father_1.mp4', :modified => Date.today, :created => Date.today}
		attributes.merge!(options)
		Clip.create!(attributes)
	end
	
	# check if the clip exists
	#
	def clip_exists?(id)
		Clip.exists?(id)
	end
	
	# create a master recording
	#
	def master_recording(options = {})
		translation_request = self.translation_request
		attributes = {:translation_request_id => translation_request.id, :title => 'My Master Recording', :language => 'German', :final_filename => 'my_file_name', :status => 'PENDING', :modified => Date.today, :created => Date.today}
		attributes.merge!(options)
		MasterRecording.create!(attributes)
	end
	
	# check if the master recording exists
	#
	def master_recording_exists?(id)
		MasterRecording.exists?(id)
	end
	
	private
		def token
			Digest::SHA1.hexdigest(Time.now.to_s + rand(12341234).to_s)[1..25]
		end
	
end