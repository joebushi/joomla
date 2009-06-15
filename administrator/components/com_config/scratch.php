		//Save user and media manager settings
		$table = &JTable::getInstance('component');

		$userpost['params'] = JRequest::getVar('userparams', array(), 'post', 'array');
		$userpost['option'] = 'com_users';
		$table->loadByOption('com_users');
		$table->bind($userpost);

		// pre-save checks
		if (!$table->check()) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// save the changes
		if (!$table->store()) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		$mediapost['params'] = JRequest::getVar('mediaparams', array(), 'post', 'array');
		$mediapost['option'] = 'com_media';
		//Sanitize $file_path and $image_path
		$file_path = $mediapost['params']['file_path'];
		$image_path = $mediapost['params']['image_path'];
		if (strpos($file_path, '/') === 0 || strpos($file_path, '\\') === 0) {
			//Leading slash.  Kill it and default to /media
			$file_path = 'images';
		}
		if (strpos($image_path, '/') === 0 || strpos($image_path, '\\') === 0) {
			//Leading slash.  Kill it and default to /media
			$image_path = 'images/stories';
		}
		if (strpos($file_path, '..') !== false) {
			//downward directories.  Kill it and default to images/
			$file_path = 'images';
		}
		if (strpos($image_path, '..') !== false) {
			//downward directories  Kill it and default to images/stories
			$image_path = 'images/stories';
		}
		$mediapost['params']['file_path'] = $file_path;
		$mediapost['params']['image_path'] = $image_path;

		$table->loadByOption('com_media');
		$table->bind($mediapost);

		// pre-save checks
		if (!$table->check()) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// save the changes
		if (!$table->store()) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}