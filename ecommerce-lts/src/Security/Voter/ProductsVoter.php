<?php 

namespace App\Security\Voter;
use App\Entity\Products;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter 
{
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    // cette propriété va nous permettre de rechercher directement le composant security
    private $security;

    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    // verifie la coherence des elements transmis
    protected function supports(string $attribute, $product):bool 
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        }
        if(!$product instanceof Products){
            return false;
        }
        return true;

        // on pt écrire aussi : 
        // return in_array($attribute, [self::EDIT, self::DELETE]) && $product instanceof Products;
    }

    /**
     * verifie si la chaine de caractere correspond à la const déclaré + un objet qui pt supporter les actions des const
     *
     * @return boolean
     */
    protected function voteOnAttribute($attribute, $product, TokenInterface $token):bool 
    {
        //on récup l'utilisateur à partir du token
        $user = $token->getUser();
        //on vérifie si l'utilisateur est connecté
        if (!$user instanceof UserInterface) return false;
        //on verifie si l'utilisateur est admin
        if($this->security->isGranted('ROLE_ADMIN')) return true;

        //si l'utilisateur n'est pas admin on vérifie les permissions
        switch ($attribute){
            case self::EDIT: 
                //On verif si l'utilisateur pt éditer
                return $this->canEdit();
                break;
            case self::DELETE:
                // On verif si l'utilisateur pt supp
                return $this->canDelete();
                break;
        }
    }

    private function canEdit(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }

    private function canDelete(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
}