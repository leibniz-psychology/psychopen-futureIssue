<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{strip}
    {if !$pageTitleTranslated}{capture assign="pageTitleTranslated"}{translate key=$pageTitle}{/capture}{/if}
    <head>
        {include file="frontend/components/headerHead.tpl"}
    </head>
{/strip}
<body>
<div>
    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th style="width: 25%">Title</th>
            <th>Section</th>
            <th>Authors</th>
            <th>Primary</th>
            <th>DOI</th>
            <th>Editors</th>
            <th>Submission</th>
            <th>Acceptance</th>
            <th>Reviews</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$result item=res}
            <tr>
                <th>{$res['articleId']}</th>
                <td class="copy_to_clip_td">
                    <span class="copy_to_clip_txt">{$res['publication']->getLocalizedTitle($res['publication']->getData('locale'))}</span>
                </td>
                <td class="copy_to_clip_td">
                    <span class="copy_to_clip_txt">{$res['section']->getLocalizedData('title', $res['publication']->getData('locale'))}</span>
                </td>
                <td class="copy_to_clip_td">
                    <div class="copy_to_clip_txt">
                        <ul class="list-group list-group-flush" style="margin: 0; padding: 0">
                            {foreach from=$res['authors'] item=author}
                                <li class="list-group-item" style="margin: 0; padding: 0">
                                    {$author->getFullName()}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </td>
                <td class="copy_to_clip_td">
                    <span class="copy_to_clip_txt">
	                    {$res['primary']->getEmail()}
                    </span>
                </td>
                <td class="copy_to_clip_td">
                    <span class="copy_to_clip_txt">
                    {foreach from=$pubIdPlugins item=pubIdPlugin}
                        {if $pubIdPlugin->getPubIdType() == 'doi'}
                            {$pubIdPlugin->getPubId($res['publication'])}{* Preview pubId *}
                        {/if}
                    {/foreach}
                    </span>
                </td>
                <td class="copy_to_clip_td">
                    <div class="copy_to_clip_txt">
                        <ul class="list-group list-group-flush" style="margin: 0; padding: 0">
                            {foreach from=$res['assignedUsers'] item=user}
                                <li class="list-group-item" style="margin: 0; padding: 0">
                                    {$user->getFullName()}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </td>
                <td class="copy_to_clip_td">
                    <span class="copy_to_clip_txt">{$res['publication']->getDateSubmitted()|date_format:"%d.%m.%Y"}</span>
                </td>
                <td class="copy_to_clip_td">
                    <span class="copy_to_clip_txt">{$res['accepted']|date_format:"%d.%m.%Y"}</span>
                </td>
                <td class="copy_to_clip_td">
                    <div class="copy_to_clip_txt">
                        {foreach from=$res['reviewRounds'] item=reviews key=key}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="mb-1 font-weight-bold">Round: {$key}</div>
                                    <ul class="list-group list-group-flush" style="margin: 0; padding: 0">
                                        {foreach from=$reviews item=name}
                                            <li class="list-group-item" style="margin: 0; padding: 0">{$name}
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
</body>
{load_script context="frontend" scripts=$scripts}
<script type="text/javascript">
    var btn = '<button class="copy_to_clip_td_btn btn btn-sm btn-secondary" style="display: none;">\n' +
        '            <i class="fas fa-copy"></i>\n' +
        '      </button>';
    $('.copy_to_clip_td').append(btn);

    $('.copy_to_clip_td_btn').click(function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).prev(".copy_to_clip_txt").text()).select();
        document.execCommand("copy");
        $temp.remove();
    });

    $('.copy_to_clip_td').mouseenter(function () {
        $(this).find('.copy_to_clip_td_btn').show();
    });

    $('.copy_to_clip_td').mouseleave(function () {
        $(this).find('.copy_to_clip_td_btn').hide();
    });
</script>
</html>
