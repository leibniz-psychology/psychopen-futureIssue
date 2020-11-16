<?php

import('lib.pkp.classes.plugins.GenericPlugin');


class FutureIssueOverview extends GenericPlugin
{
    /**
     * @copydoc Plugin::getDisplayName()
     */
    function getDisplayName()
    {
        return __('plugins.generic.issue.overview.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    function getDescription()
    {
        return __('plugins.generic.issue.overview.description');
    }

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null)
    {
        if (parent::register($category, $path, $mainContextId)) {
            if ($this->getEnabled()) {
                // Register callbacks.
                HookRegistry::register('LoadHandler', array($this, 'callbackLoadHandler'));
                HookRegistry::register('TemplateManager::fetch', array($this, 'templateFetchCallback'));
                $this->_registerTemplateResource();
            }
            return true;
        }
        return false;
    }

    /**
     * Adds additional links to submission files grid row
     * @param $hookName string The name of the invoked hook
     * @param $args array Hook parameters
     */
    public function templateFetchCallback($hookName, $params)
    {
        $request = $this->getRequest();
        $router = $request->getRouter();
        $dispatcher = $router->getDispatcher();
        $templateMgr = $params[0];
        $resourceName = $params[1];
        if ($resourceName == 'controllers/grid/gridRow.tpl') {
            $row = $templateMgr->getTemplateVars('row');
            $gridId = $row->getGridId();
            if ($gridId == 'grid-issues-futureissuegrid') {
                $data = $row->getData();
                if (!empty($data) && !empty($data->getId())) {
                    import('lib.pkp.classes.linkAction.request.OpenWindowAction');
                    $row->addAction(new LinkAction(
                        'table',
                        new OpenWindowAction(
                            $dispatcher->url($request, ROUTE_PAGE, null, 'futureIssue', 'table', $data->getId(), null)
                        ),
                        __('plugins.generic.issue.overview.link'),
                        null
                    ));
                }
            }

        }
    }

    public function callbackLoadHandler($hookName, $args)
    {
        $page = $args[0];
        $op = $args[1];
        switch ("$page/$op") {
            case 'futureIssue/view':
            case 'futureIssue/table':
                define('HANDLER_CLASS', 'FutureIssueHandler');
                define('ISSUE_PLUGIN_NAME', $this->getName());
                $args[2] = $this->getPluginPath() . '/' . 'FutureIssueHandler.inc.php';
                break;
        }
        return false;
    }

}
