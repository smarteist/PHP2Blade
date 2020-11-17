# PHP to Blade Converter

PHP2Blade is a php library for converting ordinary php view files to laravel blade template engine syntax.

## Installation

```bash
git clone https://github.com/smarteist/PHP2Blade.git
```

## Usage
By default you only need to pass your files directory to the converter,
your output files will be saved in ```/out``` directory in the same project.
```bash
php php2blade <files directory> <optional output directory>
```
To removing comments we can add ```--removecomments``` flag:
```bash
php php2blade --removecomments <files directory> <optional output directory>
```
## Output Examples
Some examples of converted code from WooCommerce templates.

![Conversion one](https://raw.githubusercontent.com/smarteist/PHP2Blade/master/img/1.png)

![Conversion two](https://raw.githubusercontent.com/smarteist/PHP2Blade/master/img/2.png)

![Conversion three](https://raw.githubusercontent.com/smarteist/PHP2Blade/master/img/3.png)


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
