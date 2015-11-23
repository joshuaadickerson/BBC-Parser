var form = {
    'type': {
        label: 'Type',
        id: 'type',
        type: 'select',
        options: {
            '0': 'Parsed content',
            '1': 'Unparsed equals',
            '2': 'Parsed equals',
        },
        change: ['changeType'],
    }
}