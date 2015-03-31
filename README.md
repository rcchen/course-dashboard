### course-dashboard

![screenshot](https://cloud.githubusercontent.com/assets/1175041/6849872/b1826326-d379-11e4-9a63-2c216208df50.png)

course-dashboard allows you to easily generate read-only student grade reports.

#### Instructions

1. Generate a Google service account. Instructions for how to do so can be found [here](https://developers.google.com/accounts/docs/OAuth2ServiceAccount)
2. Download the *.p12 file that is created and move it to the `private` directory.
3. Copy `private/config.php.sample` to `private/config.php` and fill out the relevant variables so it works with your Google Sheet.
4. Check that it works locally by invoking `php -S localhost:8000` in the root directory.
5. Move to `~/cgi-bin` in your AFS space to make it publically accessible.

If you need any assistance at all, feel free to shoot me an email at rogchen [at] cs.stanford.edu. If you find any problems, please file an issue, and as always, pull requests are welcome!

#### Copyright

(c) 2015 Roger Chen. Released under the MIT License.
