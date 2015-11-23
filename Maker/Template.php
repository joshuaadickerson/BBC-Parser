<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BBC Maker</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="bootstrap-tagsinput/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.3/css/bootstrap-select.min.css">

    <style>
        .code {
            height: auto;
            max-height: 10em;
            overflow: auto !important;
            word-break: normal !important;
            word-wrap: normal !important;
            width: 30em;
            margin-bottom: .5em;
        }
    </style>

</head>
<body>
<div class="container-fluid">
    <div id="top">
        <h1>BBC Maker</h1>
        <div id="info"><p>I tried to make this tool as easy as possible to use. However, it does require some instructions.
            First and foremost, this isn't perfect. The only way to know if it will work is to test it on your own forum.
            This is my disclaimer: I make no guarantee or warranty that this will work or won't break your forum.
            That being said, whatever you put in to this is what you'll get out of it.
            This tool requires Javascript. If you don't have it enabled, you won't get anything out of it.</p>

            <p>That brings me to the next point. If you want to do an empty string, you have to put that empty string
            (with quotes) in the form field. In order to support PHP, you need to enclose your strings in quotes. See
            the examples (default tags) to understand that better. If you leave a field blank, it won't appear in the end result.
            You do not have to add commas at the end. Several fields will always appear: type, tag, length, block_level, and autolink.
            I tried to add as many safeguards as possible. There are a lot of rules for the BBC parser that I have
            attempted to add in here.</p>

            <p>So, how do you use it? You can select a default tag and make changes to that or you can start with your own.
            Start by selecting the appropriate type (it will hide/show fields). Then put what you want in each of the fields.
            When you're done, see the results. Just copy them to your application.</p>
        </div>
    </div>
    <form method="get" class="form-horizontal">
        <div class="form-group">
            <label for="default_tags" class="col-md-2 control-label">Default Tags</label>
            <div class="col-md-6">
                <select class="form-control" id="default_tags">
                    <option value="">New BBC</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="attr_type" class="col-md-2 control-label">Type</label>
            <div class="col-md-6">
                <select name="attr_type" class="form-control" id="attr_type">
                    <option value="0">Codes::TYPE_PARSED_CONTENT</option>
                    <option value="1">Codes::TYPE_UNPARSED_EQUALS</option>
                    <option value="2">Codes::TYPE_PARSED_EQUALS</option>
                    <option value="3">Codes::TYPE_UNPARSED_CONTENT</option>
                    <option value="4">Codes::TYPE_CLOSED</option>
                    <option value="5">Codes::TYPE_UNPARSED_COMMAS</option>
                    <option value="6">Codes::TYPE_UNPARSED_COMMAS_CONTENT</option>
                    <option value="7">Codes::TYPE_UNPARSED_EQUALS_CONTENT</option>
                </select>
            </div>
        </div>
        <div class="form-group" id="tag-group">
            <label for="tag" class="col-md-2 control-label">Tag</label>
            <div class="col-md-6">
                <input type="text" name="tag" class="form-control" id="tag" data-string-type="string">
            </div>
        </div>
        <div class="form-group" id="test-group">
            <label for="test" class="col-md-2 control-label">Test</label>
            <div class="col-md-6">
                <input type="text" name="test" class="form-control stringpicker" id="test">
                <p class="help-block">A regular expression to test whether the string after the equals matches.</p>
                <p class="help-block test-blocked-help" style="display:none">Test is only available when the type is one of: unparsed_content, closed, unparsed_commas_content, or unparsed_equals_content.</p>
            </div>
        </div>
        <div class="form-group content-type" id="content-group">
            <label for="content" class="col-md-2 control-label">Content</label>
            <div class="col-md-6">
                <textarea name="content" class="form-control stringpicker" id="content"></textarea>
                <p class="help-block content-blocked-help" style="display:none">Content is only available when the type is one of: unparsed_content, closed, unparsed_commas_content, or unparsed_equals_content.</p>
            </div>
        </div>
        <div class="form-group non-content" id="before-group">
            <label for="before" class="col-md-2 control-label">Before</label>
            <div class="col-md-6">
                <textarea name="before" class="form-control stringpicker" id="before"></textarea>
            </div>
        </div>
        <div class="form-group non-content">
            <label for="after" class="col-md-2 control-label">After</label>
            <div class="col-md-6">
                <textarea name="after" class="form-control stringpicker" id="after"></textarea>
            </div>
        </div>
        <div class="form-group content-type">
            <label for="disabled_content" class="col-md-2 control-label">Disabled Content</label>
            <div class="col-md-6">
                <textarea name="disabled_content" class="form-control stringpicker" id="disabled_content"></textarea>
                <p class="help-block content-blocked-help" style="display:none">Disabled content is only available when the type is one of: unparsed_content, closed, unparsed_commas_content, or unparsed_equals_content.</p>
            </div>
        </div>
        <div class="form-group non-content">
            <label for="disabled_before" class="col-md-2 control-label">Disabled Before</label>
            <div class="col-md-6">
                <textarea name="disabled_before" class="form-control stringpicker" id="disabled_before"></textarea>
            </div>
        </div>
        <div class="form-group non-content">
            <label for="disabled_after" class="col-md-2 control-label">Disabled After</label>
            <div class="col-md-6">
                <textarea name="disabled_after" class="form-control stringpicker" id="disabled_after"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="block_level" class="col-md-2 control-label">Block Level</label>
            <div class="col-md-6">
                <input name="block_level" type="checkbox" class="form-control" id="block_level">
            </div>
        </div>
        <div class="form-group">
            <label for="trim" class="col-md-2 control-label">Trim</label>
            <div class="col-md-6">
                <select name="trim" class="form-control" id="trim">
                    <option value=""></option>
                    <option value="1">Inside</option>
                    <option value="2">Outside</option>
                    <option value="3">Both</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="validate" class="col-md-2 control-label">Validate</label>
            <div class="col-md-6">
                <textarea name="validate" class="form-control stringpicker" id="validate"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="quoted" class="col-md-2 control-label">Quotes</label>
            <div class="col-md-6">
                <select name="quoted" class="form-control" id="quoted">
                    <option value=""></option>
                    <option value="1">Required</option>
                    <option value="-1">Optional</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="require_parents" class="col-md-2 control-label">Required Parents</label>
            <div class="col-md-6">
                <select multiple name="require_parents" class="form-control tagsinput" id="require_parents"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="require_children" class="col-md-2 control-label">Required Children</label>
            <div class="col-md-6">
                <select multiple type="text" name="require_children" class="form-control tagsinput" id="require_children"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_parents" class="col-md-2 control-label">Disallowed Parents</label>
            <div class="col-md-6">
                <select multiple type="text" name="disallow_parents" class="form-control tagsinput" id="disallow_parents"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_children" class="col-md-2 control-label">Disallowed Children</label>
            <div class="col-md-6">
                <select multiple type="text" name="disallow_children" class="form-control tagsinput" id="disallow_children"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_before" class="col-md-2 control-label">Disallowed Before</label>
            <div class="col-md-6">
                <textarea name="disallow_before" class="form-control stringpicker" id="disallow_before"></textarea>
                <p class="help-block">If this BBC is a child of a code that has disallowed it, this will be output before it and it will not be parsed.</p>
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_after" class="col-md-2 control-label">Disallowed After</label>
            <div class="col-md-6">
                <textarea name="disallow_after" class="form-control stringpicker" id="disabled_after"></textarea>
                <p class="help-block">If this BBC is a child of a code that has disallowed it, this will be output after it and it will not be parsed.</p>
            </div>
        </div>
        <div class="form-group">
            <label for="parsed_tags_allowed" class="col-md-2 control-label">Parsed Tags Allowed</label>
            <div class="col-md-6">
                <select multiple type="text" name="parsed_tags_allowed" class="form-control tagsinput" id="parsed_tags_allowed"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="autolink" class="col-md-2 control-label">Autolink</label>
            <div class="col-md-6">
                <input name="autolink" type="checkbox" class="form-control" id="autolink">
            </div>
        </div>
        <div class="form-group">
            <label for="no_cache" class="col-md-2 control-label">Not Cacheable</label>
            <div class="col-md-6">
                <input name="no_cache" type="checkbox" class="form-control" id="no_cache">
            </div>
        </div>

        <div class="form-group" id="attr-param">
            <label class="col-md-2 control-label">Parameters</label>
            <div class="col-md-6">
                <div class="input_fields_wrap">
                    <p>Parameters are only allowed for parsed content and unparsed content types.</p>
                    <button class="btn add_field_button">Add More Parameters</button>
                    <button class="btn remove_all_params_btn">Remove All Parameters</button>
                </div>
            </div>
        </div>

        <input type="submit">

        <div class="params" style="display:none">
            <fieldset class="col-md-offset-2 col-md-8">
                <button class="btn remove_field">Remove</button>
                <div class="form-group">
                    <label class="col-md-2 control-label">Name</label>
                    <div class="col-md-6">
                        <input type="text" name="param_name[]" class="form-control param_name" data-string-type="string">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Match (regex)</label>
                    <div class="col-md-6">
                        <input type="text" name="param_match[]" class="form-control param_match stringpicker">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Quoted</label>
                    <div class="col-md-6">
                        <select name="param_quoted[]" class="form-control param_quoted">
                            <option value=""></option>
                            <option value="1">Required</option>
                            <option value="-1">Optional</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Validate</label>
                    <div class="col-md-6">
                        <textarea name="param_validate[]" class="form-control param_validate stringpicker"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Value</label>
                    <div class="col-md-6">
                        <input type="text" name="param_value[]" class="form-control param_value stringpicker">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Optional</label>
                    <div class="col-md-6">
                        <input type="checkbox" name="param_optional[]" class="form-control param_optional">
                    </div>
                </div>
            </fieldset>
        </div>
    </form>

    <div>
        <h2>Results</h2>
        <div>
            <h3>JSON</h3>
            <pre id="js-results" class="prettyprint language-javascript"></pre>
        </div>
        <div>
            <h3>PHP</h3>
            <pre id="php-results" class="prettyprint language-php"></pre>
        </div>
    </div>
</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script src="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.3/js/bootstrap-select.min.js"></script>
<script src="default-tags.js"></script>
<script src="maker.js"></script>
</html>