const loading = {
    progress: {
        func: [{
                call: 'color',
                modifier: 'temp',
                argument: '#ffb000'
            },
            {
                call: 'fail',
                modifier: 'temp',
                argument: '#6e0000'
            },
            {
                call: 'location',
                modifier: 'temp',
                argument: 'top'
            },
            {
                call: 'transition',
                modifier: 'temp',
                argument: {
                    speed: '1.5s',
                    opacity: '0.6s',
                    termination: 400
                }
            }
        ]
    }
}
export let routes = [{
        path: '/dashboard',
        component: require('./components/ExampleComponent.vue').default,
        meta: loading
    },
    {
        path: '/profile',
        component: require('./components/Profile.vue').default,
        meta: loading
    },
    {
        path: '/developer',
        component: require('./components/Developer.vue').default,
        meta: loading
    },
    {
        path: '/users',
        component: require('./components/Users.vue').default,
        meta: loading
    },
    {
        path: '*',
        component: require('./components/error404.vue').default,
        meta: loading
    }
]
