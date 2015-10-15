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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
    <script type="text/javascript" src="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.js"></script>

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
            <label for="type" class="col-md-2 control-label">Default Tags</label>
            <div class="col-md-8">
                <select class="form-control" id="default_tags">
                    <option value="">New BBC</option>
                    <option value="abbr">abbr</option>
                    <option value="anchor">anchor</option>
                    <option value="b">b</option>
                    <option value="br">br</option>
                    <option value="center">center</option>
                    <option value="code">code</option>
                    <option value="code1">code1</option>
                </select>
            </div>
            <script>
                var default_tags = {
                    'abbr': {
                        'tag': 'abbr',
                        'attr_type': 1,
                        'before': '\'<abbr title="$1">\'',
                        'after': '\'</abbr>\'',
                        'quoted': 'optional',
                        'disabled_after': '\' ($1)\'',
                        'block_level': false,
                        'autolink': true,
                    },
                    'anchor': {
                        'tag': 'anchor',
                        'attr_type': 1,
                        'test': '\'[#]?([A-Za-z][A-Za-z0-9_\-]*)\'',
                        'before': '\'<span id="post_$1">\'',
                        'after': '\'</span>\'',
                        'block_level': false,
                        'autolink': true,
                    },
                    'b': {
                        'tag': 'b',
                        'attr_type': 0,
                        'before': '\'<strong class="bbc_strong">\'',
                        'after': '\'</strong>\'',
                        'block_level': false,
                        'autolink': true,
                    },
                    'br': {
                        'tag': 'br',
                        'attr_type': 4,
                        'content': '\'<br />\'',
                        'block_level': false,
                        'autolink': false,
                    },
                    'center': {
                        'tag': 'center',
                        'attr_type': 0,
                        'before': '\'<div class="centertext">\'',
                        'after': '\'</div>\'',
                        'block_level': true,
                        'autolink': true,
                    },
                    'code': {
                        'tag': 'code',
                        'attr_type': 3,
                        'content': '\'<div class="codeheader">\' . $txt[\'code\'] . \': <a href="javascript:void(0);" onclick="return elkSelectText(this);" class="codeoperation">\' . $txt[\'code_select\'] . \'</a></div><pre class="bbc_code prettyprint">$1</pre>\'',
                        'validate': '$this->isDisabled(\'code\') ? null : function(&$tag, &$data, $disabled) {\n\t$data = tabToHtmlTab($data);\n}',
                        'block_level': true,
                        'autolink': false,
                    },
                    'code1': {
                        'tag': 'code',
                        'attr_type': 7,
                        'content': '\'<div class="codeheader">\' . $txt[\'code\'] . \': ($2) <a href="#" onclick="return elkSelectText(this);" class="codeoperation">\' . $txt[\'code_select\'] . \'</a></div><pre class="bbc_code prettyprint">$1</pre>\'',
                        'validate': '$this->isDisabled(\'code\') ? null : function(&$tag, &$data, $disabled) {\n\t$data = tabToHtmlTab($data);\n}',
                        'block_level': true,
                        'autolink': false,
                    },
                };
            </script>
        </div>
        <div class="form-group">
            <?= \BBC\displayErrors($errors, 'type') ?>
            <label for="type" class="col-md-2 control-label">Type</label>
            <div class="col-md-8">
                <select name="attr_type" class="form-control" id="attr_type">
                    <option value="0">Codes::TYPE_PARSED_CONTENT</option>
                    <option value="1">Codes::TYPE_UNPARSED_EQUALS</option>
                    <option value="2">Codes::TYPE_PARSED_EQUALS</option>
                    <option value="3">Codes::TYPE_UNPARSED_CONTENT</option>
                    <option value="4">Codes::TYPE_CLOSED</option>
                    <option value="5">Codes::TYPE_UNPARSED_COMMAS</option>
                    <option value="6">Codes::TYPE_UNPARSED_COMMAS_CONTENT</option>
                    <option value="7">Codes::TYPE_UNPARSED_EQUALS_CONTENT</option>

                    <?php
                    /*

                    foreach ($export->getTypes() as $value => $name)
                    {
                        echo '
                        <option value="', $value, '"', $selected['type'] === $value ? ' selected' : '', '>', $name, '</option>';
                    }
*/
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <?= \BBC\displayErrors($errors, 'tag'); ?>
            <label for="a" class="col-md-2 control-label">Tag</label>
            <div class="col-sm-8">
                <input type="text" name="tag" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="a" class="col-md-2 control-label">Test</label>
            <div class="col-sm-8">
                <input type="text" name="test" class="form-control">
            </div>
        </div>
        <div class="form-group content-type">
            only for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content
            <label for="content" class="col-md-2 control-label">Content</label>
            <div class="col-sm-8">
                <textarea name="content" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group non-content">
            <label for="before" class="col-md-2 control-label">Before</label>
            <div class="col-sm-8">
                <textarea name="before" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group non-content">
            <label for="after" class="col-md-2 control-label">After</label>
            <div class="col-sm-8">
                <textarea name="after" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group content-type">
            only for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content
            <label for="disabled_content" class="col-md-2 control-label">Disabled Content</label>
            <div class="col-sm-8">
                <textarea name="disabled_content" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group non-content">
            <label for="disabled_before" class="col-md-2 control-label">Disabled Before</label>
            <div class="col-sm-8">
                <input type="text" name="disabled_before" class="form-control">
            </div>
        </div>
        <div class="form-group non-content">
            <label for="disabled_after" class="col-md-2 control-label">Disabled After</label>
            <div class="col-sm-8">
                <input type="text" name="disabled_after" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="block_level" class="col-md-2 control-label">Block Level</label>
            <div class="col-sm-8">
                <input name="block_level" type="checkbox" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="trim" class="col-md-2 control-label">Trim</label>
            <div class="col-md-8">
                <select name="type" class="form-control">
                    <option value=""></option>
                    <option value="">Inside</option>
                    <option value="">Outside</option>
                    <option value="">Both</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="validate" class="col-md-2 control-label">Validate</label>
            <div class="col-sm-8">
                <textarea name="validate" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="quoted" class="col-md-2 control-label">Quotes</label>
            <div class="col-md-8">
                <select name="quoted" class="form-control">
                    <option value=""></option>
                    <option value="">Required</option>
                    <option value="">Optional</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="require_parents" class="col-md-2 control-label">Require Parents</label>
            <div class="col-sm-8">
                <select multiple name="require_parents" class="form-control" data-role="tagsinput" id="require_parents"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="require_children" class="col-md-2 control-label">Require Children</label>
            <div class="col-sm-8">
                <select multiple type="text" name="require_children" class="form-control" data-role="tagsinput" id="require_children"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_parents" class="col-md-2 control-label">Disallow Parents</label>
            <div class="col-sm-8">
                <select multiple type="text" name="disallow_parents" class="form-control" data-role="tagsinput" id="require_parents"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_children" class="col-md-2 control-label">Disallow Children</label>
            <div class="col-sm-8">
                <select multiple type="text" name="disallow_children" class="form-control" data-role="tagsinput" id="disallow_children"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_before" class="col-md-2 control-label">Disallow Before</label>
            <div class="col-sm-8">
                <input type="text" name="disallow_before" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_after" class="col-md-2 control-label">Disallow After</label>
            <div class="col-sm-8">
                <input type="text" name="disallow_after" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="parsed_tags_allowed" class="col-md-2 control-label">Parsed Tags Allowed</label>
            <div class="col-sm-8">
                <select multiple type="text" name="parsed_tags_allowed" class="form-control" data-role="tagsinput" id="parsed_tags_allowed"></select>
            </div>
        </div>
        <div class="form-group">
            <label for="autolink" class="col-md-2 control-label">Autolink</label>
            <div class="col-sm-8">
                <input name="autolink" type="checkbox" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="no_cache" class="col-md-2 control-label">Not Cacheable</label>
            <div class="col-sm-8">
                <input name="no_cache" type="checkbox" class="form-control">
            </div>
        </div>
        <div class="form-group" id="attr-param">
            <label for="no_cache" class="col-md-2 control-label">Parameters</label>
            <div class="col-sm-8">
                <div class="input_fields_wrap">
                    <button class="add_field_button">Add More Fields</button>
                </div>
            </div>
        </div>

        <input type="submit">

        <div class="form-group params" style="display:none">
            <button class="remove_field">Remove</button>
            <div class="col-md-offset-4 col-sm-8">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="fatal" class="col-md-2 control-label">Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="param_name[]" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fatal" class="col-md-2 control-label">Match (regex)</label>
                        <div class="col-sm-8">
                            <input type="text" name="param_match[]" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fatal" class="col-md-2 control-label">Quoted</label>
                        <div class="col-sm-8">
                            <select name="param_quoted[]" class="form-control">
                                <option value=""></option>
                                <option value="required">Required</option>
                                <option value="optional">Optional</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fatal" class="col-md-2 control-label">Validate</label>
                        <div class="col-sm-8">
                            <textarea name="param_validate[]" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fatal" class="col-md-2 control-label">Value</label>
                        <div class="col-sm-8">
                            <input type="text" name="param_value[]" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fatal" class="col-md-2 control-label">Optional</label>
                        <div class="col-sm-8">
                            <input type="checkbox" name="param_optional[]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div>
        <h2>Results</h2>
        <pre id="raw-results" class="prettyprint language-javascript"></pre>
        <pre id="js-results" class="prettyprint language-javascript"></pre>
        <pre id="php-results" class="prettyprint language-php"></pre>
    </div>
</div>
</body>
<script>
    $(document).ready(function() {
        var max_params      = 10;
        var wrapper         = $(".input_fields_wrap"); // fields wrapper
        var add_button      = $(".add_field_button"); // add button ID
        var param_div       = $('.params');

        // Content can only be used for unparsed_content (3), closed (4), unparsed_commas_content (6), and unparsed_equals_content (7)
        var content_types = ['3', '4', '6', '7'];
        // Parameters can only be used for parsed_content (0) and unparsed_content (3)
        var param_types = ['0', '3'];

        var num_params = 1;
        $(add_button).click(function(e) {
            e.preventDefault();
            // Don't allow more params than allowed
            if(num_params < max_params) {
                num_params++;
                $(wrapper).append(param_div.clone().show());
            }

            parse();
        });

        $(wrapper).on("click",".remove_field", function(e) {
            e.preventDefault();
            $(this).parent('div').remove();
            num_params--;

            parse();
        })

        /*$('input').tagsInput({
            tagClass: 'big',
            // return, comma, space, tab, semi-colon keys
            // (only return and comma are working)
            confirmKeys: [13, 44, 32, 9, 59],
            maxTags: 20,
            maxChars: 25,
            trimValue: true
        });*/

        // When the type changes, do stuff
        var changeType = function () {
            var val = $('#attr_type').val();

            if (content_types.indexOf(val) !== -1)
            {
                $('.content-type').show();
                $('.content-type .form-control').prop('disabled', false);

                $('.non-content').hide();
                $('.non-content .form-control').prop('disabled', true);
            }
            else
            {
                $('.content-type').hide();
                $('.content-type .form-control').prop('disabled', true);

                $('.non-content').show();
                $('.non-content .form-control').prop('disabled', false);
            }

            if (param_types.indexOf(val) !== -1)
            {
                $('#attr-param').show();
            }
            else
            {
                $('#attr-param').hide();
            }
        };
        changeType();
        $('#attr_type').change(changeType);

        var isValidTag = function(tag) {
            var re = /^[a-z0-9_]+$/;

            if (!tag.match(re)) {
                console.log('bad tag name');
                // @todo add error
            }
        };
        var checkTag = function () {
            return isValidTag($(this).val());
        };
        $('input[name="tag"]').change(checkTag);

        var parse = function () {
            // Get all of the form values
            var form = $('.form-control:enabled').serializeArray();
            var results = {};

            $.each(form, function(i, val) {
                results[val.name] = val.value;
            });

            // For some reason serializeArray() only grabs the last element in arrays
            results['require_parents']       = $('#require_parents').val();
            results['require_children']      = $('#require_children').val();
            results['disallow_parents']      = $('#disallow_parents').val();
            results['disallow_children']     = $('#disallow_children').val();
            results['parsed_tags_allowed']   = $('#parsed_tags_allowed').val();

            var bbc = {
                'self::ATTR_TAG':           results.tag,
                'self::ATTR_TYPE':          parseInt(results.attr_type),
                'self::ATTR_LENGTH':        results.tag.length,
                'self::ATTR_BLOCK_LEVEL':   typeof results.block_level !== 'undefined' && results.block_level ? true : false,
                'self::ATTR_AUTOLINK':      typeof results.autolink !== 'undefined' && results.autolink ? true : false,
            };

            if (typeof results.test !== 'undefined' && results.test != '')
            {
                bbc['self::ATTR_TEST'] = results.test;
            }

            if (typeof results.content !== 'undefined' && results.content != '')
            {
                bbc['self::ATTR_CONTENT'] = results.content;
            }

            if (typeof results.disabled_content !== 'undefined' && results.disabled_content != '')
            {
                bbc['self::ATTR_DISABLED_CONTENT'] = results.disabled_content;
            }

            if (typeof results.before !== 'undefined' && results.before != '')
            {
                bbc['self::ATTR_BEFORE'] = results.before;
            }

            if (typeof results.disabled_before !== 'undefined' && results.disabled_before != '')
            {
                bbc['self::ATTR_DISABLED_BEFORE'] = results.disabled_before;
            }

            if (typeof results.after !== 'undefined' && results.after != '')
            {
                bbc['self::ATTR_AFTER'] = results.after;
            }

            if (typeof results.disabled_after !== 'undefined' && results.disabled_after != '')
            {
                bbc['self::ATTR_DISABLED_AFTER'] = results.disabled_after;
            }

            if (typeof results.trim !== 'undefined' && results.trim != '')
            {
                bbc['self::ATTR_TRIM'] = results.trim;
            }

            if (typeof results.validate !== 'undefined' && results.validate != '')
            {
                bbc['self::ATTR_VALIDATE'] = results.validate;
            }

            if (typeof results.quoted !== 'undefined' && results.quoted != '')
            {
                bbc['self::ATTR_QUOTED'] = results.quoted;
            }

            if (typeof $('#require_parents').val() !== 'undefined' && $('#require_parents').val() !== null)
            {
                bbc['self::ATTR_REQUIRE_PARENTS'] = $('#require_parents').val();
            }

            if (typeof $('#require_children').val() !== 'undefined' && $('#require_children').val() !== null)
            {
                bbc['self::ATTR_REQUIRE_CHILDREN'] = $('#require_children').val();
            }

            $('#raw-results').text(JSON.stringify(bbc));
            $('#js-results').text(parse_js(results));
            $('#php-results').text(parse_php(bbc));
        };

        var parse_php = function (results) {
console.log(results);
            var php = '\
array(\n\
    ';

            $(results).each(function (i, ele) {
               // console.log(i);
                php += '\t';
                if (typeof ele === 'object')
                {
                    $(ele).each(function(k, v) {
                        php += '\'' + v + '\', ';
                    });
                }
                else if (typeof ele === 'string')
                {
                    php += ele;
                }
                else
                {
                    console.log(ele);
                }

                php += ',\n';
            });

            return php;
        };

        parse_js = function (results) {
            return JSON.stringify(results);
        }

        $('form').change(parse);

        $('#default_tags').change(function (){
            var tag = $(this).val();

            $('.form-control').each(function () {
                var ele = $(this);
                var name = ele.attr('name');

                // If it doesn't have a name, it wasn't meant for this
                if (typeof name === 'undefined')
                {
                    return;
                }

                if (typeof default_tags[tag][name] !== 'undefined')
                {
                    ele.val(default_tags[tag][name]);
                }
                else
                {
                    ele.val('');
                }
            });
            changeType();
            //$('input[name="tag"]').val(tag);
        });

        parse();

        prettyPrint();


    });
</script>
</html>