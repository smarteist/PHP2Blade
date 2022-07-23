# PHP To Blade Transpiler

PHP2Blade is a php transpiler for converting ordinary php view files to laravel blade template engine files.

### Installation

```bash
git clone https://github.com/smarteist/PHP2Blade.git
```
Then run:
```bash
composer install
```

### Usage
By default you only need to pass directory of your files to the transpiler,
your output files will be saved in ```/out``` directory in the same project.
```bash
php php2blade <files directory> <optional output directory>
```
To remove comments we can add ```--removecomments``` switch
```bash
php php2blade --removecomments <files directory> <optional output directory>
```
To prevent comments conversion add ```--keepcomments```
```bash
php php2blade --keepcomments <src directory> <-optional- output directory>
```
### Output Examples
Some production ready outputs converted by PHP2Blade:

![Conversion one](https://raw.githubusercontent.com/smarteist/PHP2Blade/master/img/1.png)

![Conversion two](https://raw.githubusercontent.com/smarteist/PHP2Blade/master/img/2.png)

![Conversion three](https://raw.githubusercontent.com/smarteist/PHP2Blade/master/img/3.png)


#### Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

#### License
[MIT](https://choosealicense.com/licenses/mit/)
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
