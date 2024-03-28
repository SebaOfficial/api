import Embed from './Newsletter.svelte';

var div = document.createElement('DIV');
var script = document.currentScript;
script.parentNode.insertBefore(div, script);

const embed = new Embed({
	target: div,
    props: {
        apiUrl: API_URL,
        reprompt: 860000,
    }
});
