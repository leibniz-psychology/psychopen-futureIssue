{extends file="layouts/backend.tpl"}

{block name="page"}
	<div id="futureIssueTable">
		<table class="pkpTable">
			<thead>
			<tr>
				<th>{translate key="plugins.generic.issue.overview.id"}</th>
				<th>{translate key="plugins.generic.issue.overview.title"}</th>
				<th>{translate key="plugins.generic.issue.overview.section"}</th>
				<th>{translate key="plugins.generic.issue.overview.authors"}</th>
				<th>{translate key="plugins.generic.issue.overview.primary"}</th>
				<th>{translate key="plugins.generic.issue.overview.doi"}</th>
				<th>{translate key="plugins.generic.issue.overview.editors"}</th>
				<th>{translate key="plugins.generic.issue.overview.subDate"}</th>
				<th>{translate key="plugins.generic.issue.overview.accDate"}</th>
				<th>{translate key="plugins.generic.issue.overview.reviews"}</th>
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
							<ul>
								{foreach from=$res['authors'] item=author}
									<li>
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
							<ul>
								{foreach from=$res['assignedUsers'] item=user}
									<li>
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
										<ul>
											{foreach from=$reviews item=name}
												<li>{$name}</li>
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
{/block}
