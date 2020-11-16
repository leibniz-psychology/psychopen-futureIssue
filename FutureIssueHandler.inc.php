<?php

import('classes.handler.Handler');

class FutureIssueHandler extends Handler
{
    protected $plugin;

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->plugin = PluginRegistry::getPlugin('generic', ISSUE_PLUGIN_NAME);
        $this->addRoleAssignment(
            array(ROLE_ID_MANAGER),
            array(
                'index', 'view', 'table',
            )
        );
    }

    /**
     * @copydoc PKPHandler::authorize()
     */
    function authorize($request, &$args, $roleAssignments)
    {
        import('lib.pkp.classes.security.authorization.PKPSiteAccessPolicy');
        $this->addPolicy(new PKPSiteAccessPolicy($request, null, $roleAssignments));
        return parent::authorize($request, $args, $roleAssignments);
    }

    public function table($args, $request)
    {

    	$context = $request->getContext();
    	$contextId = $context ? $context->getId() : CONTEXT_SITE;
        $assignmentDao =& DAORegistry::getDAO('UserStageAssignmentDAO');
        $editDecisionDao =& DAORegistry::getDAO('EditDecisionDAO');
        $reviewDao =& DAORegistry::getDAO('ReviewAssignmentDAO');
        $pubIdPlugins = PluginRegistry::loadCategory('pubIds', true);
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pubIdPlugins', $pubIdPlugins);
        $issueId = isset($args[0]) ? (int)$args[0] : null;
	    $submissionsIterator =Services::get('submission')->getMany([
		    'contextId' => $contextId,
		    'issueIds' => $issueId,
		    'status' => STATUS_SCHEDULED,
	    ]);
        $result = array();
        foreach ($submissionsIterator as $submission) {
	        $publication = $submission->getCurrentPublication();
	        $sectionDao = DAORegistry::getDAO('SectionDAO'); /** @var $sectionDao SectionDAO */
	        if ($sectionId = $publication->getData('sectionId')) {
		        $section = $sectionDao->getById($sectionId);
	        }
            $submissionId = $submission->getId();
            $editDecisions = $editDecisionDao->getEditorDecisions($submissionId);
            $accepted = null;
            $reviews = $reviewDao->getBySubmissionId($submissionId);
            $rounds = array();
            foreach ($reviews as $review) {
                if ($review->getStatus()>6)
                    $rounds[$review->getRound()][] = $review->getReviewerFullName();
            }
            foreach (array_reverse($editDecisions) as $editDecision) {
                if ($editDecision['decision'] == SUBMISSION_EDITOR_DECISION_ACCEPT) {
                    $accepted = $editDecision['dateDecided'];
                }
            }
	        $primaryContact = Services::get('author')->get($publication->getData('primaryContactId'));
            $authors = $publication->getData('authors');
            $editors = $assignmentDao->getUsersBySubmissionAndStageId($submissionId, 1)->toArray();
            $count = 0;
            foreach ($editors as $editor) {
                foreach ($authors as $author) {
                    if ($editor->getEmail() == $author->getEmail() || $editor->getFullName() == $author->getFullName()) {
                        unset($editors[$count]);
                        break;
                    }
                }
                $count++;
            }
            $art_res = array(
                "articleId" => $submission->getId(),
                "section" => $section,
                "accepted" => $accepted,
                "publication" => $submission,
                "authors" => $authors,
                "primary" => $primaryContact,
                "assignedUsers" => $editors,
                "reviewRounds" => $rounds
            );
            $result[] = $art_res;
        }
        $templateMgr->assign(array(
                'result' => $result
            )
        );
        return $templateMgr->display($this->plugin->getTemplateResource('table.tpl'));
    }
}
