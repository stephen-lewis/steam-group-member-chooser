<?php

declare(strict_types=1);

namespace App\Controller;

use App\SteamGroupChooserFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function random_int;

class MainController extends AbstractController
{
    /**
     * @param string  $steamApiKey
     * @param Request $request
     * @param string  $group
     * @Route("/{group}")
     * @Route("/steam-group-member-chooser/{group}", name="steam-group-chooser")
     *
     * @return Response
     * @throws Exception
     */
    public function steamGroupMemberChooser(string $steamApiKey, Request $request, string $group = '') : Response
    {
        $form = $this->createForm(SteamGroupChooserFormType::class, ['groupName' => $group]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupName = trim($form->getData()['groupName']);

            $url = "http://steamcommunity.com/groups/" . $groupName . "/memberslistxml/?xml=1";
            $xml = simplexml_load_string(file_get_contents($url));

            $members = array();
            $memberCount = $xml->memberCount;
            for ($i = 0; $i < $memberCount; $i++) {
                $members[] = $xml->members->steamID64[$i];
            }

            $winner = $members[random_int(0, $memberCount - 1)];

            $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . $steamApiKey . "&steamids=" . $winner . "&format=xml";
            $xml = simplexml_load_string(file_get_contents($url));

            return $this->render(
                'steam_group_member_chooser.html.twig',
                [
                    'members' => $members,
                    'winner' => $xml->players->player->personaname,
                    'winnerAvatar' => $xml->players->player->avatarfull,
                    'memberCount' => $memberCount,
                    'groupName' => $groupName,
                ]
            );
        }

        return $this->render('steam_group_member_chooser.html.twig', ['form' => $form->createView()]);
    }
}
