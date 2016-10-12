DESCRIPTION
-----------
This module provides an easy way to import previously exported Views definitions.

INSTALLATION
-----------
1. Create 'sites/all/imports/views' folder.
2. Export the Views you wish to import and paste the export code into separate text files with '.inc' extension.
3. Place the newly created text files in the 'sites/all/imports/views' subdirectory.
4. Do not forget to add '<?php' line to the top of the files.
5. Ensure all dependant modules are installed for the fields in the views.
6. Install this module.

MAIN IDEA
-----------
The idea is that you could have a collection of commonly used views in a folder.
Then, when you start a project, copy the required views definitions into this
module and install.
