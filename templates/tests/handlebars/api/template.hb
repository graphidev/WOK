{{!--
<div class="entry">
    <h1>{{title}}</h1>
     <div class="body">
        {{body}}
    </div>
</div>
--}}

    {{#if session}}
        <div class="session">
            {{#if session.firstname}} {{#if session.lastname}}
            Connecté en tant que : {{session.firstname}} {{session.lastname}}
            {{/if}}{{/if}}
            {{#if session.error}}
                Erreur de connexion
            {{/if}}
        
        </div>
    {{/if}}


{{#each people}} <div class="entry">{{firstName}} {{lastName}}</div> {{/each}}