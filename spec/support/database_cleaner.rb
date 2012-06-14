class DatabaseCleaner
	
	# Truncate the given tables in the database
	#
	def truncate_tables(tables)
		tables.each do |table|
			ActiveRecord::Base.connection.execute("TRUNCATE TABLE #{table}")
		end
	end
	
end