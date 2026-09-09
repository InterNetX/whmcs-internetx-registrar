<?php

namespace MGModule\InterNetX\Addon\Core\Models;


class ServerGroups extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'tblservergroups';
    public $timestamps = false;

    public function servers()
    {
        $servers = [];
        foreach ($this->relatedServers as $relatedServer) {
            $servers[] = Service\Server::find($relatedServer->serverid);
        }

        return $servers;
    }

    public function relatedServers()
    {
        return $this->hasMany('MGModule\InterNetX\Addon\Core\Models\ServerGroups\ServerGroupsRel', 'groupid', 'id');
    }
}