<?php
declare(strict_types=1);

namespace App\Controller;

class PagesController extends AppController
{
    /**
     * Page d'accueil simple (ton home)
     */
    public function home()
    {
            $user = $this->request->getSession()->read('Auth.User');
            $this->set(compact('user'));
    }


    /**
     * Pages statiques optionnelles (si tu veux garder CakePHP default behavior)
     */
    public function display(...$path)
    {
        // Si aucune page → redirection home
        if (empty($path)) {
            return $this->redirect(['action' => 'home']);
        }

        // Sécurité anti ../
        if (in_array('..', $path, true)) {
            throw new \Cake\Http\Exception\ForbiddenException();
        }

        $page = $path[0] ?? null;
        $subpage = $path[1] ?? null;

        $this->set(compact('page', 'subpage'));

        return $this->render(implode('/', $path));
    }
}
