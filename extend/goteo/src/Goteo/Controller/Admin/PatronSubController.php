<?php
/**
 * Gestion de padrinos
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Message,
	Goteo\Application\Config,
    Goteo\Model;

class PatronSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nuevo apadrinamiento',
      'edit' => 'Editando Apadrinamiento',
      'reorder' => 'Ordenando los padrinos en Portada',
      'view' => 'Apadrinamientos',
    );


    static protected $label = 'Padrinos';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function reorderAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('reorder', $id, $this->getFilters(), $subaction));
    }


    public function viewAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('view', $id, $this->getFilters(), $subaction));
    }

    public function activeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('active', $id, $this->getFilters(), $subaction));
    }

    public function removeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove', $id, $this->getFilters(), $subaction));
    }

    public function add_homeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add_home', $id, $this->getFilters(), $subaction));
    }

    public function remove_homeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove_home', $id, $this->getFilters(), $subaction));
    }

    public function downAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('down', $id, $this->getFilters(), $subaction));
    }

    public function upAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('up', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

        $node = $this->node;

        $errors = array();

        // guardar cambios en registro de apadrinamiento
        if ($this->isPost() && $this->hasPost('save')) {

            // manteniendo el orden
            $order = Model\Patron::next($this->getPost('user'), $node);

            // objeto
            $promo = new Model\Patron(array(
                'id' => $this->getPost('id'),
                'node' => $node,
                'project' => $this->getPost('project'),
                'user' => $this->getPost('user'),
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'link' => $this->getPost('link'),
                'order' => $order,
                'active' => $this->getPost('active')
            ));

			if ($promo->save($errors)) {
                if ($this->getPost('action') == 'add') {
                    Message::info('Proyecto apadrinado correctamente');

                    $projectData = Model\Project::getMini($this->getPost('project'));
                    $userData = Model\User::getMini($this->getPost('user'));

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    $log->populate('nuevo proyecto apadrinado (admin)', '/admin/patron',
                        \vsprintf('El admin %s ha hecho al usuario %s padrino del proyecto %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('user', $userData->name, $userData->id),
                            Feed::item('project', $projectData->name, $projectData->id)
                    )));
                    $log->doAdmin('admin');
                    unset($log);
                }

                // tratar si han marcado pendiente de traducir
                if ($this->hasPost('pending') && $this->getPost('pending') == 1
                    && !Model\Patron::setPending($promo->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

                return $this->redirect('/admin/patron/view/'.$this->getPost('user'));
            }
			else {
                Message::error('El registro no se ha grabado correctamente. '. implode(', ', $errors));
                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'patron',
                                'file' => 'edit',
                                'action' => 'add',
                                'promo' => $promo,
                                'available' => Model\Patron::available(null, $node),
                                'status' => $status
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'patron',
                                'file' => 'edit',
                                'action' => 'edit',
                                'promo' => $promo,
                                'available' => Model\Patron::available($promo->project, $node),
                        );
                        break;
                }
			}
		}

        // aplicar cambio de orden
        if ($this->isPost() && $this->hasPost('apply_order')) {
            foreach ($this->getPost()->all() as $key => $value) {
                $parts = explode('_', $key);

                if ($parts[0] == 'order') {
                    Model\Patron::setOrder($parts[1], $value);
                }
            }
        }


        switch ($action) {
            case 'active':
                $set = $flag == 'on' ? true : false;
                Model\Patron::setActive($id, $set);

                if ($this->hasGet('user'))
                    return $this->redirect('/admin/patron/view/'.$this->getGet('user'));
                else
                    return $this->redirect('/admin/patron/');

                break;

            case 'remove':
                $patron = Model\Patron::get($id);
                if (Model\Patron::delete($id)) {
                    $projectData = Model\Project::getMini($patron->project);

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    $log->populate('proyecto desapadrinado (admin)', '/admin/promote',
                        \vsprintf('El admin %s ha %s del proyecto %s', array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('relevant', 'Quitado el apadrinamiento'),
                        Feed::item('project', $projectData->name, $projectData->id)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                } else {
                    Message::error('No se ha podido quitar correctamente el apadrinamiento');
                }

                if ($this->hasGet('user'))
                    return $this->redirect('/admin/patron/view/'.$this->getGet('user'));
                else
                    return $this->redirect('/admin/patron/');

                break;

            case 'add':

                $user = ($this->hasGet('user')) ? (object) array('id'=>$this->getGet('user')) : null;

                return array(
                        'folder' => 'patron',
                        'file' => 'edit',
                        'action' => 'add',
                        'promo' => (object) array('user' => $user),
                        'available' => Model\Patron::available(null, $node)
                );
                break;

            case 'edit':
                $promo = Model\Patron::get($id);

                return array(
                        'folder' => 'patron',
                        'file' => 'edit',
                        'action' => 'edit',
                        'promo' => $promo,
                        'available' => Model\Patron::available($promo->project, $node),
                );
                break;

            case 'add_home':
                  if (Model\Patron::add_home($id)) {
                    return $this->redirect('/admin/patron');
                }
                break;

            case 'remove_home':
                  if (Model\Patron::remove_home($id)) {
                    return $this->redirect('/admin/patron');
                }
                break;

            case 'reorder':
                // promos by user
                $patrons = array();
                $patroned = Model\Patron::getAll($node);

                foreach ($patroned as $promo) {
                    if (!isset($patrons[$promo->user->id])&&($promo->order)) {
                        $patrons[$promo->user->id] = (object) array(
                            'id' => $promo->user->id,
                            'name' => $promo->user->name,
                            'order' => $promo->order,
                            'home' => $promo->home
                        );
                    }
                }

                return array(
                        'folder' => 'patron',
                        'file' => 'order',
                        'patrons' => $patrons
                );
                break;

                case 'up':

                Model\Patron::up($id);
                return $this->redirect('/admin/patron/reorder');
                break;

            case 'down':

                Model\Patron::down($id);

                return $this->redirect('/admin/patron/reorder');
                break;

            case 'view':
                // promos by user
                $promos  = array();
                $patrons = array();
                $patroned = Model\Patron::getAll($node);

                foreach ($patroned as $promo) {
                    if (!isset($patrons[$promo->user->id])) {
                        $patrons[$promo->user->id] = (object) array(
                            'id' => $promo->user->id,
                            'name' => $promo->user->name,
                            'order' => $promo->order
                        );
                    }
                    $promos[$promo->user->id][] = $promo;
                }

                return array(
                        'folder' => 'patron',
                        'file' => 'view',
                        'patron' => $patrons[$id],
                        'promos' => $promos[$id]
                );
                break;
        }

        // promos by user
        $patrons = array();
        $patroned = Model\Patron::getAll($node);

        foreach ($patroned as $promo) {
            if (!isset($patrons[$promo->user->id])) {
                $patrons[$promo->user->id] = (object) array(
                    'id' => $promo->user->id,
                    'name' => $promo->user->name,
                    'order' => $promo->order,
                    'home' => $promo->home
                );
            }
        }

        return array(
                'folder' => 'patron',
                'file' => 'list',
                'patrons' => $patrons
        );

    }

}
