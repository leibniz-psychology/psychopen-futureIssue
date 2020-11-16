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
        $publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');
        $authorDao =& DAORegistry::getDAO('AuthorDAO');
        $assignmentDao =& DAORegistry::getDAO('UserStageAssignmentDAO');
        $editDecisionDao =& DAORegistry::getDAO('EditDecisionDAO');
        $reviewDao =& DAORegistry::getDAO('ReviewAssignmentDAO');
        $pubIdPlugins = PluginRegistry::loadCategory('pubIds', true);
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pubIdPlugins', $pubIdPlugins);
        $issueId = isset($args[0]) ? (int)$args[0] : null;
        $articles = $publishedArticleDao->getPublishedArticles($issueId);
        $result = array();
        foreach ($articles as $article) {
            $articleId = $article->getId();
            $editDecisions = $editDecisionDao->getEditorDecisions($articleId);
            $accepted = null;
            $reviews = $reviewDao->getBySubmissionId($articleId);
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
            $authors = $authorDao->getBySubmissionId($articleId);
            $editors = $assignmentDao->getUsersBySubmissionAndStageId($articleId, 1)->toArray();
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
                "articleId" => $articleId,
                "accepted" => $accepted,
                "article" => $article,
                "authors" => $authors,
                "primary" => $authorDao->getPrimaryContact($articleId),
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