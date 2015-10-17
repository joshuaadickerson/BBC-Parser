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
        'validate': '$this->isDisabled(\'code\') ? null : function(&$tag, &$data, $disabled) {\n\
        $data = tabToHtmlTab($data);\n\
        }',
        'block_level': true,
        'autolink': false,
    },
    'code1': {
        'tag': 'code',
        'attr_type': 7,
        'content': '\'<div class="codeheader">\' . $txt[\'code\'] . \': ($2) <a href="#" onclick="return elkSelectText(this);" class="codeoperation">\' . $txt[\'code_select\'] . \'</a></div><pre class="bbc_code prettyprint">$1</pre>\'',
        'validate': '$this->isDisabled(\'code\') ? null : function(&$tag, &$data, $disabled) {\n\
        $data = tabToHtmlTab($data);\n\
        }',
        'block_level': true,
        'autolink': false,
    },

    'color': {
        'tag': 'color',
        'attr_type': 1,
        'test': '\'(#[\\da-fA-F]{3}|#[\\da-fA-F]{6}|[A-Za-z]{1,20}|rgb\\((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\s?,\\s?){2}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\))\'',
        'before': '\'<span style="color: $1;" class="bbc_color">\'',
        'after': '\'</span>\'',
        'block_level': false,
        'autolink': true,
    },

    'email': {
        'tag': 'email',
        'attr_type': 1,
        'before': '\'<a href="mailto:$1" class="bbc_email">\'',
        'after': '\'</a>\'',
        'disallow_children': ['email', 'url', 'iurl'],
        'disabled_after': '\' ($1)\'',
        'block_level': false,
        'autolink': false,

    },

    'footnote': {
        'tag': 'footnote',
        'attr_type': 0,
        'before': '\'<sup class="bbc_footnotes">%fn%\'',
        'after': '\'%fn%</sup>\'',
        'disallow_parents': [
            'footnote',
            'code',
            'anchor',
            'url',
            'iurl',
        ],
        'disallow_before': '\'\'',
        'disallow_after': '\'\'',
        'block_level': true,
        'autolink': true,
    },
    'font': {
        'tag': 'font',
        'attr_type': 1,
        'test': '\'[A-Za-z0-9_,\-\s]+?\'',
        'before': '\'<span style="font-family: $1;" class="bbc_font">\'',
        'after': '\'</span>\'',
        'block_level': false,
        'autolink': true,
    },
    'hr': {
        'tag': 'hr',
        'attr_type': 4,
        'content': '\'<hr />\'',
        'block_level': true,
        'autolink': false,
    },
    'i': {
        'tag': 'i',
        'attr_type': 0,
        'before': '\'<em>\'',
        'after': '\'</em>\'',
        'block_level': false,
        'autolink': true,
    },
    'img': {
        'tag': 'img',
        'attr_type': 3,
        'params': {
            'alt': {
                'param_optional': true,
            },
            'width': {
                'param_optional': true,
                'param_value': '\'width:100%;max-width:$1px;\'',
                'param_match': '\'(\\d+)\'',
            },
            'height': {
                'param_optional': true,
                'param_value': '\'max-height:$1px;\'',
                'param_match': '\'(\\d+)\'',
            },
        },
        'content': '\'<img src="$1" alt="{alt}" style="{width}{height}" class="bbc_img resized" />\'',
        'validate': 'function(&$tag, &$data, $disabled) {\n\
		if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)\n\
		{\n\
			$data = \'http://\' . $data;\n\
		}\n\
	}',
        'disabled_content': '\'($1)\'',
        'block_level': false,
        'autolink': false,
    },
    'img1': {
        'tag': 'img',
        'attr_type': 3,
        'content': '\'<img src="$1" alt="" class="bbc_img" />\'',
        'validate': 'function(&$tag, &$data, $disabled) {\n\
		if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)\n\
		{\n\
			$data = \'http://\' . $data;\n\
		}\n\
	}',
        'disabled_content': '\'($1)\'',
        'block_level': false,
        'autolink': false,
    },
    'iurl': {
        'tag': 'iurl',
        'attr_type': 3,
        'content': '\'<a href="$1" class="bbc_link">$1</a>\'',
        'validate': 'function(&$tag, &$data, $disabled) {\n\
		if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)\n\
		{\n\
			$data = \'http://\' . $data;\n\
		}\n\
	}',
        'block_level': false,
        'autolink': false,
    },
    'iurl1': {
        'tag': 'iurl',
        'attr_type': 1,
        'before': '\'<a href="$1" class="bbc_link">\'',
        'after': '\'</a>\'',
        'validate': 'function(&$tag, &$data, $disabled) {\n\
		if ($data[0] === \'#\')\n\
		{\n\
			$data = \'#post_\' . substr($data, 1);\n\
		}\n\
		elseif (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)\n\
		{\n\
			$data = \'http://\' . $data;\n\
		}\n\
	}',
        'disallow_children': ['email', 'url', 'iurl'],
        'disabled_after': '\' ($1)\'',
        'block_level': false,
        'autolink': false,
    },
    'left': {
        'tag': 'left',
        'attr_type': 0,
        'before': '\'<div style="text-align: left;">\'',
        'after': '\'</div>\'',
        'block_level': true,
        'autolink': true,
    },
    'li': {
        'tag': 'li',
        'attr_type': 0,
        'before': '\'<li>\'',
        'after': '\'</li>\'',
        'trim': 2,
        'require_parents': ['list'],
        'block_level': true,
        'disabled_before': '\'\'',
        'disabled_after': '\'<br />\'',
        'autolink': true,
    },
    'list': {
        'tag': 'list',
        'attr_type': 0,
        'before': '\'<ul class="bbc_list">\'',
        'after': '\'</ul>\'',
        'trim': 1,
        'require_children': ['li', 'list'],
        'block_level': true,
        'autolink': true,
    },
    'list1': {
        'tag': 'list',
        'attr_type': 0,
        'params': {
            'type': {
                'param_match': '\'(none|disc|circle|square|decimal|decimal-leading-zero|lower-roman|upper-roman|lower-alpha|upper-alpha|lower-greek|lower-latin|upper-latin|hebrew|armenian|georgian|cjk-ideographic|hiragana|katakana|hiragana-iroha|katakana-iroha)\'',
            },
        },
        'before': '\'<ul class="bbc_list" style="list-style-type: {type};">\'',
        'after': '\'</ul>\'',
        'trim': 1,
        'require_children': ['li'],
        'block_level': true,
        'autolink': true,
    },
    'me': {
        'tag': 'me',
        'attr_type': 1,
        'before': '\'<div class="meaction">&nbsp;$1 \'',
        'after': '\'</div>\'',
        'quoted': -1,
        'block_level': true,
        'disabled_before': '\'/me \'',
        'disabled_after': '\'<br />\'',
        'autolink': true,
    },
    'member': {
        'tag': 'member',
        'attr_type': 1,
        'test': '\'[\d*]\'',
        'before': '\'<span class="bbc_mention"><a href="\' . $scripturl . \'?action=profile;u=$1">@',
        'after': '\'</a></span>\'',
        'disabled_before': '\'@\'',
        'disabled_after': '\'\'',
        'block_level': false,
        'autolink': true,
    },
    'nobbc': {
        'tag': 'nobbc',
        'attr_type': 3,
        'content': '\'$1\'',
        'block_level': false,
        'autolink': true,

    },
    'pre': {
        'tag': 'pre',
        'attr_type': 0,
        'before': '\'<pre class="bbc_pre">\'',
        'after': '\'</pre>\'',
        'block_level': false,
        'autolink': true,
    },
    'quote': {
        'tag': 'quote',
        'attr_type': 0,
        'before': '\'<div class="quoteheader">\' . $txt[\'quote\'] . \'</div><blockquote>\'',
        'after': '\'</blockquote>\'',
        'block_level': true,
        'autolink': true,

    },
    'quote1': {
        'tag': 'quote',
        'attr_type': 0,
        'params': {
            'author': {
                'param_match': '\'(.{1,192}?)\'',
                'param_quoted': -1,
            },
        },
        'before': '\'<div class="quoteheader">\' . $txt[\'quote_from\'] . \': {author}</div><blockquote>\'',
        'after': '\'</blockquote>\'',
        'block_level': true,
        'autolink': true,

    },
    'quote2': {
        'tag': 'quote',
        'attr_type': 2,
        'before': '\'<div class="quoteheader">\' . $txt[\'quote_from\'] . \': $1</div><blockquote>\'',
        'after': '\'</blockquote>\'',
        'quoted': -1,
        'parsed_tags_allowed': ['url', 'iurl'],
        'block_level': true,
        'autolink': true,

    },
    'quote3': {
        'tag': 'quote',
        'attr_type': 0,
        'params': {
            'author': {
                'param_match': '\'([^<>]{1,192}?)\'',
            },
            'link': {
                'param_match': '\'(?:board=\d+;)?((?:topic|threadid)=[\\dmsg#\./]{1,40}(?:;start=[\\dmsg#\./]{1,40})?|msg=\\d{1,40}|action=profile;u=\\d+)\'',
            },
            'date': {
                'param_match': '\'(\\d+)\'',
                'validate': '\'htmlTime\'',
            },
        },
        'before': '\'<div class="quoteheader"><a href="\' . $scripturl . \'?{link}">\' . $txt[\'quote_from\'] . \': {author} \' . ($modSettings[\'todayMod\'] == 3 ? \' - \' : $txt[\'search_on\']) . \' {date}</a></div><blockquote>\'',
        'after': '\'</blockquote>\'',
        'block_level': true,
        'autolink': true,

    },
    'quote4': {
        'tag': 'quote',
        'attr_type': 0,
        'params': {
            'author': {
                'param_match': '\'(.{1,192}?)\''
            },
        },
        'before': '\'<div class="quoteheader">\' . $txt[\'quote_from\'] . \': {author}</div><blockquote>\'',
        'after': '\'</blockquote>\'',
        'block_level': true,
        'autolink': true,

    },
    'right': {
        'tag': 'right',
        'attr_type': 0,
        'before': '\'<div style="text-align: right;">\'',
        'after': '\'</div>\'',
        'block_level': true,
        'autolink': true,

    },
    's': {
        'tag': 's',
        'attr_type': 0,
        'before': '\'<del>\'',
        'after': '\'</del>\'',
        'block_level': false,
        'autolink': true,
    },
    'size': {
        'tag': 'size',
        'attr_type': 1,
        'test': '[1-7]{1}$',
        'before': '\'<span style="font-size: $1;" class="bbc_size">\'',
        'after': '\'</span>\'',
        'validate': 'function(&$tag, &$data, $disabled) {\n\
		$sizes = {1: 0.7, 2: 1.0, 3: 1.35, 4: 1.45, 5: 2.0, 6: 2.65, 7: 3.95);\n\
		$data = $sizes[(int) $data] . \'em\';\n\
	}',
        'disallow_parents': ['size'],
        'disallow_before': '\'<span>\'',
        'disallow_after': '\'</span>\'',
        'block_level': false,
        'autolink': true,
    },
    'size1': {
        'tag': 'size',
        'attr_type': 1,
        'test': '\'([1-9][\\d]?p[xt]|small(?:er)?|large[r]?|x[x]?-(?:small|large)|medium|(0\\.[1-9]|[1-9](\\.[\\d][\\d]?)?)?em)\'',
        'before': '\'<span style="font-size: $1;" class="bbc_size">\'',
        'after': '\'</span>\'',
        'disallow_parents': ['size'],
        'disallow_before': '<span>',
        'disallow_after': '</span>',
        'block_level': false,
        'autolink': true,
    },
    'spoiler': {
        'tag': 'spoiler',
        'attr_type': 0,
        'before': '\'<span class="spoilerheader">\' . $txt[\'spoiler\'] . \'</span><div class="spoiler"><div class="bbc_spoiler" style="display: none;">\'',
        'after': '\'</div></div>\'',
        'block_level': true,
        'autolink': true,
    },
    'sub': {
        'tag': 'sub',
        'attr_type': 0,
        'before': '\'<sub>\'',
        'after': '\'</sub>\'',
        'block_level': false,
        'autolink': true,
    },
    'sup': {
        'tag': 'sup',
        'attr_type': 0,
        'before': '\'<sup>\'',
        'after': '\'</sup>\'',
        'block_level': false,
        'autolink': true,
    },
    'table': {
        'tag': 'table',
        'attr_type': 0,
        'before': '\'<div class="bbc_table_container"><table class="bbc_table">\'',
        'after': '\'</table></div>\'',
        'trim': 1,
        'require_children': ['tr'],
        'block_level': true,
        'autolink': true,

    },
    'td': {
        'tag': 'td',
        'attr_type': 0,
        'before': '\'<td>\'',
        'after': '\'</td>\'',
        'require_parents': ['tr'],
        'trim': 2,
        'block_level': true,
        'disabled_before': '\'\'',
        'disabled_after': '\'\'',
        'autolink': true,
    },
    'th': {
        'tag': 'th',
        'attr_type': 0,
        'before': '\'<th>\'',
        'after': '\'</th>\'',
        'require_parents': ['tr'],
        'trim': 2,
        'block_level': true,
        'disabled_before': '\'\'',
        'disabled_after': '\'\'',
        'autolink': true,
    },
    'tr': {
        'tag': 'tr',
        'attr_type': 0,
        'before': '\'<tr>\'',
        'after': '\'</tr>\'',
        'require_parents': ['table'],
        'require_children': ['td', 'th'],
        'trim': 3,
        'block_level': true,
        'disabled_before': '\'\'',
        'disabled_after': '\'\'',
        'autolink': true,
    },
    'tt': {
        'tag': 'tt',
        'attr_type': 0,
        'before': '\'<span class="bbc_tt">\'',
        'after': '\'</span>\'',
        'block_level': false,
        'autolink': true,
    },
    'u': {
        'tag': 'u',
        'attr_type': 0,
        'before': '\'<span class="bbc_u">\'',
        'after': '\'</span>\'',
        'block_level': false,
        'autolink': true,
    },
    'url': {
        'tag': 'url',
        'attr_type': 3,
        'content': '\'<a href="$1" class="bbc_link" target="_blank">$1</a>\'',
        'validate': 'function(&$tag, &$data, $disabled) {\n\
		if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)\n\
		{\n\
			$data = \'http://\' . $data;\n\
		}\n\
	}',
        'block_level': false,
        'autolink': false,
    },
    'url1': {
        'tag': 'url',
        'attr_type': 1,
        'before': '\'<a href="$1" class="bbc_link" target="_blank">\'',
        'after': '\'</a>\'',
        'validate': 'function(&$tag, &$data, $disabled) {\n\
		if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)\n\
		{\n\
			$data = \'http://\' . $data;\n\
		}\n\
	}',
        'disallow_children': ['email', 'url', 'iurl'],
        'disabled_after': '\' ($1)\'',
        'block_level': false,
        'autolink': false,
    },
};