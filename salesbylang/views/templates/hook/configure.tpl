<h1>{l s='SALES BY LANGUAGE' mod='salesbylang'}</h1>
<p>{l s='Get customer info of your stores sales by language' mod='salesbylang'}</p>

{if $updateURL}
    <div class="alert alert-success" role="alert">
        {l s='Form submited succesfuly' mod='salesbylang'}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
{/if}

<form method="post">
    <div class="form-group">
        <select class="form-control" name="language">
            {foreach $languages as $language}
                <option name="{$language[1]}" value="{$language[0]}" {if $lang == $language[0]} selected {/if}>{$language[1]}</option>
            {/foreach}
        </select>
        <input class="form-controll"  type="date" value="{$fromDate}" name="fromDate" placeholder="{l s='Date From ...' mod='salesbylang'}">
        <input class="form-controll" type="date" value="{$toDate}" name="toDate" placeholder="{l s='Date To...' mod='salesbylang'}">
    </div>
    <button class="btn btn-primary" name="submitConfig" type="submit">
        {l s='Get Data' mod='salesbylang'}
    </button>
</form>

{if $updateURL}
    <table>
        <tr>
            <th>{l s='Name' mod='salesbylang'}</th>
            <th>{l s='Surname' mod='salesbylang'}</th>
            <th>{l s='Email' mod='salesbylang'}</th>
            <th>{l s='Language' mod='salesbylang'}</th>
        </tr>   
        {foreach $users as $user}
            <tr>
                <td>{$user["firstname"]}</td>
                <td>{$user["surname"]}</td>
                <td>{$user["email"]}</td>
                <td>{$user["lang"]}</td>
            </tr>
        {/foreach}
    </table>

    <a class="btn btn-primary" href="{$shop}/modules/salesbylang/excel.csv" download>
        {l s='DOWNLOAD SPREEDSHEET' mod='salesbylang'}
    <a>
{/if}
