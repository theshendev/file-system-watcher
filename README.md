# Laravel File System Watcher

A PHP-based file system watcher for Laravel 11.x that monitors the `storage/app/watched` directory for file creations, modifications, and deletions, performing specific actions based on file type.

## Features
- **JPG Optimization**: Compresses JPG files to 80% quality on creation or modification using `intervention/image`.
- **JSON Posting**: Sends JSON files to `https://fswatcher.requestcatcher.com/` on creation or modification.
- **TXT Appending**: Appends random text from the Bacon Ipsum API to TXT files on creation or modification.
- **ZIP Extraction**: Extracts ZIP files to a subdirectory on creation using `ZipArchive`.
- **Meme Replacement**: Replaces deleted files with a random meme image from the Meme API.

## Requirements
- PHP 8.2 or higher
- Laravel 11.x
- PHP GD or Imagick extension enabled
- Composer
- Internet access for API calls (Bacon Ipsum, Meme API)

## Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/theshendev/file-system-watcher.git
   cd file-system-watcher
   
   ```
 
 2. **Install Dependencies**:

   ```bash
   composer install
   
    ```
    
    ## Usage
    Start the file system watcher to monitor storage/app/watched
    
      ```bash
    php artisan watch:files
      ```


