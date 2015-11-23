$(document).ready(function() {
    var max_params      = 10;
    var num_params      = 0;
    var wrapper         = $('.input_fields_wrap'); // fields wrapper
    var add_button      = $('.add_field_button');
    var param_div       = $('.params');

    // Content can only be used for unparsed_content (3), closed (4), unparsed_commas_content (6), and unparsed_equals_content (7)
    var content_types = ['3', '4', '6', '7'];
    // Parameters can only be used for parsed_content (0) and unparsed_content (3)
    var param_types = ['0', '3'];
    // ATTR_TEST is only used in equals and commas types
    var test_types = ['1', '2', '5', '6', '7'];

    var addParameter = function(name, values) {
        // Don't allow more params than allowed
        if(num_params >= max_params)
        {
            return;
        }

        var new_param = param_div.clone().show();
        if (name && values)
        {
            new_param.find('.param_name').val(name);

            $.each(values, function (k, v) {
                if (typeof v === 'boolean')
                {
                    new_param.find('.' + k).prop('checked', v);
                }
                else
                {
                    new_param.find('.' + k).val(v);
                }
            });

        }

        $(wrapper).append(new_param);
        num_params++;
        //$('.stringpicker').each(addStringPicker);
        $('.stringpicker').selectpicker('refresh');
    };

    var removeAllParameters = function() {
        wrapper.children('.params').remove();
        num_params = 0;
    };

    add_button.click(function(e) {
        e.preventDefault();
        addParameter();
        parse();
    });

    $('.remove_all_params_btn').click(function(e) {
        e.preventDefault();
        removeAllParameters();
        parse();
    });

    $(wrapper).on('click', '.remove_field', function(e) {
        e.preventDefault();

        $(this).parent('div').remove();
        num_params--;

        parse();
    });

    // Add tagsInput()
    $('.tagsinput').each(function (){
        $(this).tagsinput({
            //tagClass: 'big',
            // return, comma, space, tab, semi-colon keys
            confirmKeys: [13, 44, 32, 9, 59],
            maxTags: 20,
            maxChars: 25,
            trimValue: true
        });
    });

    // When the type changes, do stuff
    var changeType = function () {
        var val = $('#attr_type').val();

        if (content_types.indexOf(val) !== -1)
        {
            $('.content-type .form-control').prop('disabled', false);
            $('.content-blocked-help').hide();
            $('.non-content .form-control').prop('disabled', true);
        }
        else
        {
            $('.content-type .form-control').prop('disabled', true);
            $('.content-blocked-help').show();
            $('.non-content .form-control').prop('disabled', false);
        }

        if (param_types.indexOf(val) !== -1)
        {
            add_button.prop('disabled', false);
        }
        else
        {
            add_button.prop('disabled', true);
            removeAllParameters();
        }

        if (test_types.indexOf(val) !== -1)
        {
            $('#test').prop('disabled', false);
            $('.test-blocked-help').hide();
        }
        else
        {
            $('#test').prop('disabled', true);
            $('.test-blocked-help').show();
        }
    };
    $('#attr_type').change(changeType);

    var isValidTag = function (tag) {
        var re = /^[a-z0-9_]+$/;

        return tag.match(re);
    };

    var checkTag = function () {
        if (!isValidTag($(this).val()))
        {
            addError($(this), 'bad-tag-name', 'The tag can only contain lowercased alphanumeric characters and underscores.');
        }
        else
        {
            removeError($(this), 'bad-tag-name');
        }
    };

    var addError = function (ele, id, text) {
        removeError(ele, id);
        ele.parent().append('<p class="help-block" id="' + id + '">' + text + '</p>');
        ele.closest('.form-group').addClass('has-error');
    };

    var removeError = function (ele, id) {
        $('#' + id).remove();
        ele.closest('.form-group').removeClass('has-error');
    }

    $('#require_parents').change(function () {
        if (arrayHasIntersection($('#disallow_parents').val(), $(this).val()))
        {
            addError($(this), 'require-parents-with-disallow-parents', 'Require parents cannot contain tags found in disallow parents');
        }
        else
        {
            removeError($(this), 'require-parents-with-disallow-parents')
        }
    });

    var arrayHasIntersection = function (array1, array2) {
        console.log(typeof array1);
        //if (typeof array1 !== 'array')
        for (var v in array1)
        {
            console.log(v);
            if ($.inArray(v, array2))
            {
                return true;
            }
        }

        return false;
    };

    var formToObject = function (ele) {
        var obj = {};
        var ele = $(ele);
        var name = ele.attr('name');
        var val = ele.val();
        var isString = ele.attr('data-string-type') == 'string';
        var isCheckbox = ele.attr('type') === 'checkbox';

        // Null values don't get added
        if (val == '' || val === null || typeof formToCodes[name] === 'undefined') {
            return;
        }

        php_name = formToCodes[name];

        // Checkbox values are on/off
        if (isCheckbox){
            if (ele.prop('checked')){
                obj[php_name] = true;
            }
        }
        // It's a string, wrap it in quotes
        else if(isString) {
            obj[php_name] = '\'' + val + '\'';
        }
        // It's a raw value
        else {
            obj[php_name] = val;
        }

        return obj;
    };

    var parse = function () {
        // Get all of the form values
        var form = $('.form-control:enabled');

        // Every BBC must have at least these attributes set
        var results = {
            'self::ATTR_TAG':           '',
            'self::ATTR_TYPE':          0,
            'self::ATTR_LENGTH':        0,
            'self::ATTR_BLOCK_LEVEL':   false,
            'self::ATTR_AUTOLINK':      false,
        };

        // Translate the form to PHP constants
        var formToCodes = {
            after:                  'self::ATTR_AFTER',
            autolink:               'self::ATTR_AUTOLINK',
            before:                 'self::ATTR_BEFORE',
            block_level:            'self::ATTR_BLOCK_LEVEL',
            content:                'self::CONTENT',
            disabled_after:         'self::ATTR_DISABLED_AFTER',
            disabled_before:        'self::ATTR_DISABLED_BEFORE',
            disabled_content:       'self::ATTR_DISABLED_CONTENT',
            disallow_after:         'self::ATTR_DISALLOW_AFTER',
            disallow_before:        'self::ATTR_DISALLOW_BEFORE',
            disallow_children:      'self::ATTR_DISALLOW_CHILDREN',
            disallow_parents:       'self::ATTR_DISALLOW_PARENTS',
            no_cache:               'self::NO_CACHE',
            /*
             'param_match[]':        'self::PARAM_ATTR_MATCH',
             'param_name[]':         'self::PARAM_ATTR_NAME',
             'param_optional[]':     'self::PARAM_ATTR_OPTIONAL',
             'param_quoted[]':       'self::PARAM_ATTR_QUOTED',
             'param_validate[]':     'self::PARAM_ATTR_VALIDATE',
             'param_value[]':        'self::PARAM_ATTR_VALUE',
             */
            parsed_tags_allowed:    'self::ATTR_PARSED_TAGS_ALLOWED',
            quoted:                 'self::ATTR_QUOTED',
            require_children:       'self::ATTR_REQUIRE_CHILDREN',
            require_parents:        'self::ATTR_REQUIRE_PARENTS',
            tag:                    'self::ATTR_TAG',
            test:                   'self::ATTR_TEST',
            trim:                   'self::ATTR_TRIM',
            attr_type:              'self::ATTR_TYPE',
            validate:               'self::VALIDATE',
        };

        $.each(form, function (i, ele) {
            //console.log($(ele));
            ele = $(ele);
            var name = ele.attr('name');
            var val = ele.val();
            var isString = ele.attr('data-string-type') == 'string';
            var isCheckbox = ele.attr('type') === 'checkbox';

            // Null values don't get added
            if (val == '' || val === null || typeof formToCodes[name] === 'undefined') {
                return;
            }

            php_name = formToCodes[name];

            // Checkbox values are on/off
            if (isCheckbox){
                if (ele.prop('checked')){
                    results[php_name] = true;
                }
            }
            // It's a string, wrap it in quotes
            else if(isString) {
                results[php_name] = '\'' + val + '\'';
            }
            // It's a raw value
            else {
                results[php_name] = val;
            }
        });

        results['self::ATTR_LENGTH'] = results['self::ATTR_TAG'].length;
        results['self::ATTR_TYPE']   = parseInt(results['self::ATTR_TYPE']);

        if (num_params > 0)
        {
            var param_groups = wrapper.children('.params');



            results['self::ATTR_PARAM'] = {};

            // Get each parameter group
            param_groups.each(function(i, v) {
                var param_group = $(this);
                var paramToCode = {
                    'name':     'self::ATTR_PARAM_NAME',
                    'match':    'self::ATTR_PARAM_MATCH',
                    'quoted':   'self::ATTR_PARAM_QUOTED',
                    'validate': 'self::ATTR_PARAM_VALIDATE',
                    'value':    'self::ATTR_PARAM_VALUE',
                    'optional': 'self::ATTR_PARAM_OPTIONAL',
                };

                var param = {};

                $.each(paramToCode, function(attr_key, attr_val){
                    var element = param_group.find('.param_' + attr_key);

                    if (element.attr('data-string-type') !== 'string')
                    {
                        param[attr_val] = element.attr('type') === 'checkbox' ? element.prop('checked') : element.val();
                    }
                    else
                    {
                        param[attr_val] = '\'' + element.val() + '\'';
                    }

                    if (typeof param[attr_val] === 'undefined' || param[attr_val] == '' || param[attr_val] == '\'\'')
                    {
                        delete param[attr_val];
                    }

                    console.log(param);
                });

                // Name and match are necessary
                if (typeof param['self::ATTR_PARAM_NAME'] !== 'undefined' && typeof param['self::ATTR_PARAM_MATCH'] !== 'undefined')
                {
                    results['self::ATTR_PARAM'][param['self::ATTR_PARAM_NAME']] = {};

                    // Filter empty elements
                    $.each(param, function (key, attr) {
                        if (key == 'self::ATTR_PARAM_NAME' || attr == '')
                        {
                            return;
                        }

                        results['self::ATTR_PARAM'][param['self::ATTR_PARAM_NAME']][key] = attr;
                    });
                }
            });
        }

        $('#js-results').text(JSON.stringify(results));
        $('#php-results').text(parse_php(results));
    };

    var parse_php = function (results) {
        var php = '\
array(\n\
    ';
        //results['self::ATTR_TAG'] = '\'' + results['self::ATTR_TAG'] + '\'';

        jQuery.each(results, function (i, ele) {
            php += '\t' + i + ' => ';

            if (i == 'self::ATTR_PARAM')
            {
                php += 'array(';
                jQuery.each(ele, function(param_name, param_attrs) {
                    php += '\n\t\t' + param_name + ' => array(\n';
                    $.each(param_attrs, function (param_attr, attr_val) {
                        php += '\t\t\t' + param_attr + ' => ' + attr_val + ',\n';
                    });
                    php += '\t\t),';
                });
                php += '\n\t)';
            }
            else if (typeof ele === 'object')
            {
                php += 'array(';

                $.each(ele, function (k, v){
                    php += '\n\t\t\'' + v + '\' => \'' + v + '\',';
                });

                php += '\n\t)';
            }
            // string, bool, int
            else
            {
                php += ele;
            }

            php += ',\n';
        });

        php += '\
);'
        return php;
    };

    parse_js = function (results) {
        return JSON.stringify(results);
    }

    $('#default_tags').change(function (){
        var tag = $(this).val();

        // Clean up any tags before adding new ones
        $('.tagsinput').tagsinput('removeAll');

        $('.form-control').each(function (key, val) {
            var ele = $(this);
            var name = ele.attr('name');

            // If it doesn't have a name, it wasn't meant for this
            if (typeof name === 'undefined' || tag == '')
            {
                return;
            }

            if (typeof default_tags[tag][name] !== 'undefined')
            {
                //console.log(typeof default_tags[tag][name]);
                // All of the arrays are just simple lists
                if (typeof default_tags[tag][name] == 'object')
                {
                    $.each(default_tags[tag][name], function (k, v){
                        $(ele).tagsinput('add', v);
                    });
                }
                else if (typeof default_tags[tag][name] === 'boolean')
                {
                    ele.prop('checked', default_tags[tag][name]);
                }
                else
                {
                    ele.val(default_tags[tag][name]);
                }
            }
            else
            {
                ele.val('');
            }
        });

        changeType();
        removeAllParameters();

        // Add parameters
        if (typeof default_tags[tag]['params'] !== 'undefined')
        {
            $.each(default_tags[tag]['params'], function (k, v) {
                addParameter(k, v);
            });
        }
    });

    // Any changes to the form results in the results changing
    $('form').change(parse);

    // Populate the default tags
    var default_tags_box = $('#default_tags');
    $.each(default_tags, function(k, v) {
        default_tags_box.append(
            $('<option/>').text(k).val(k)
        );
    });

    $('#tag').change(checkTag);

    parse();

    prettyPrint();

    changeType();

    var p = 0;
    var addStringPicker = function () {
        var parent_input = $(this);
        var stringpicker_div = $('<div class="col-md-2"></div>');
        var stringpicker = $('<select class="selectpicker"><option value="raw" data-icon="fa fa-file-code-o">Raw/code</option><option value="string" data-icon="fa fa-file-text-o">String</option></select>');

        stringpicker.change(function () {
            parent_input.attr('data-string-type', $(this).val());
            parse();
        });

        stringpicker_div.append(stringpicker);

        $(this).parents('.form-group').append(stringpicker_div);
        console.log('added ' + p++);
    };

    $('.stringpicker').each(addStringPicker);
});