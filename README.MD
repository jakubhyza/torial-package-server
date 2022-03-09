# Torial Package Manager - Repository
This is official but simple repository software for Torial CMS.
Feel free to contrabute on this project or make your own versions of this project.

For you who don't know - Torial CMS is my own modular system written in PHP for crating administration interfaces.
It is not public yet because API changes a lot. Just few websites are running closed alfa version. 

## How to set up repository
Simply upload this project on a web server with installed PHP and set properties in main.json. All props are required. Here is an example:

```JSON
{
	"repository_name": "A Torial Repository",
	"repository_path": "https://example.com/path/to/repo/",
	"repository_operator": "Your Name",
	"repository_contact": "me@example.com",


	"server_software": "Torial Package Manager - Repository",
	"server_version": 2
}
```

`repository_name` - You can name your repository however you want. This will show in the client.

`repository_path` - Url to the repository. Include whole path. Don't forget `/` at the end. HTTPS recommended.

`repository_operator` - Your name or organization name.

`repository_contact` - Your e-mail or a link to your website.

`server_software` - Don't touch it! Or maybe you can if you made any changes. This is just for debug purpose.

`server_version` - Alright, do not touch this in any case. This is the version of the used protocol. 
| Version   | State                 |
|-----------|-----------------------|
| 1.0 - 1.4 | Deprecated            |
| 1.5       | Deprecated but in use |
| 2         | Actual version        |

Don't forget to add your repository to your Torial config.

## Add packages

Upload packages/modules into `/files/` folder (Without zip, just put them here as a folder) and then just run cron.php to regenerate index.

## How to contrabute or make own repository software?
**This applies to protocol version `2`**

In fact there are only two important files (can change in other versions).
- First important file is `%repo_path%/index.json` containing basic information about the repo. (you already know the structure)
- The second one is `%repo_path%/indices/main.json` containing full index of the repository.

Client sends HTTP/HTTPS `GET` request for this files. Expected content type for response is `application/json`

**Structure of the index**:
```JSON
{
	"repository_info": "https://example.com/path/to/repo/index.json",
	"repository_uri": "https://example.com/path/to/repo/",
	"document_type": "index",
	"timestamp": 1646857311,
	"length": 26,
	"modules": []
}
```
`repository_info` - Backwards compatibility with `v1.2`. You don't need to put it here.

`repository_uri` - URL of the repository. Same as `repository_path` in `index.json`

`document_type` - The type of response. (Look at the table down)

`timestamp` - Timestamp of when the index was generated (for cache purposes)

`length` - Count of the modules (plugins) in the index

`modules` - The list of the modules (Look at the structure down)

**Document types**
| Type     | Description                                                      |
|----------|------------------------------------------------------------------|
| `index`  | List of information about modules in repository                  |
| `search` | Backwards compatibility with `v1.2` where search was server-side |
| `bundle` | Future use                                                       |
| `repo`   | index.json                                                       |

**Module structure**
```JSON
{
	"id": "example.module",
	"name": "Example module",
	"author": "An awesome guy",
	"version": 1,
	"dependencies": {
		"example.library": "^1"
	},
	"readme": "https://example.com/path/to/readme.md",
	"icon": "https://example.com/path/to/icon_file.png",
	"download": "https://example.com/downloads/example.module.zip"
	}
```
`id`, `name`, `author`, `version` and `dependencies` are just copied data from `manifest.json` file inside the module.

`readme` and `icon` can be found as files inside the module as well but you need to make them accessable by URL.

`download` is the URL to download the module. Client is expecting zip file with module folder inside.
