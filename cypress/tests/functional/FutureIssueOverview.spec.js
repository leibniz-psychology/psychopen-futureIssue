describe('FutureIssueOverview plugin tests', function () {

	beforeEach('Perform Login', function () {
		cy.login('admin', 'admin', 'publicknowledge');
	})

	it('Disable FutureIssueOverview', function () {
		cy.get('ul[id="navigationPrimary"] a:contains("Settings")').click();
		cy.get('ul[id="navigationPrimary"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// disable plugin if enabled
		cy.get('input[id^="select-cell-futureissueoverview-enabled"]')
			.then($btn => {
				if ($btn.attr('checked') === 'checked') {
					cy.get('input[id^="select-cell-futureissueoverview-enabled"]').click();
					cy.get('div[class*="pkp_modal_panel"] button[class*="pkpModalConfirmButton"]').click();
					cy.get('div:contains(\'The plugin "Future Issue Table View" has been disabled.\')');
				}
			});
	});

	it('Enable FutureIssueOverview', function () {
		cy.get('ul[id="navigationPrimary"] a:contains("Settings")').click();
		cy.get('ul[id="navigationPrimary"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// Find and enable the plugin
		cy.get('input[id^="select-cell-futureissueoverview-enabled"]').click();
		cy.get('div:contains(\'The plugin "Future Issue Table View" has been enabled.\')');
	});
});
