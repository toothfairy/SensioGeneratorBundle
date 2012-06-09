
    /**
     * Creates a new {{ entity }} entity.
     *
{% if 'annotation' == format %}
     * @Route("/create", name="{{ route_name_prefix }}_create")
     * @Method("post")
     * @Template("{{ bundle }}:{{ entity }}:new.html.twig")
     * @Secure(roles="ROLE_USER")
{% endif %}
     */
    public function createAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $entity  = new {{ entity_class }}();
        $request = $this->getRequest();
        $form    = $this->createForm(new {{ entity_class }}Type(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

{% if secure -%}
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($entity);
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);

{%- endif %}
            {% if 'show' in actions -%}
                return $this->redirect($this->generateUrl('{{ route_name_prefix }}_show', array('id' => $entity->getId())));
            {%- else -%}
                return $this->redirect($this->generateUrl('{{ route_name_prefix }}'));
            {%- endif %}

        }

{% if 'annotation' == format %}
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
{% else %}
        return $this->render('{{ bundle }}:{{ entity|replace({'\\': '/'}) }}:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
{% endif %}
    }
