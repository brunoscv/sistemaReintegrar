<?php

class Dashboard_model extends CI_Model
{
	public $table = "frequencias";

	public function __construct()
	{
		parent::__construct();
	}

	public function setConsultas() {

 		return $this->db->query(" INSERT INTO frequencias SELECT NULL, h.id as horarios_id, 
 		 					  				  ds.id as dia_semana_id, ic.id as item_consulta_id, 
 		 					  				  c.id as cod_consulta, NULL, 0,1, 
 		 					  				  DATE_FORMAT(NOW(), '%Y-%m-%d') as createdAt, NULL
							      FROM item_consulta as ic
							 	  INNER JOIN (horarios_profissionais as hp, horarios as h, dias_semana as ds, 
							 				  periodo_consulta as pc, consultas as c)
							 	  ON (ic.horarios_id = hp.id AND hp.horarios_id = h.id AND ic.dia_semana_id = ds.id 
							          AND ic.periodo_consulta_id = pc.id AND ic.consultas_id = c.id)
							 	  WHERE ic.dia_semana_id = DATE_FORMAT(NOW(),'%w') AND month(pc.data) = DATE_FORMAT(NOW(),'%m') AND ic.status = '1'")
					->result();
	}

	public function createViewCalendarioProfissionais($periodo_consulta_id, $profissional_id) {
		return $this->db->query(" SELECT ic.id, h.desc_horario, ds.desc_dia_semana, pc.data, 
										 ic.status, c.id AS cod_consulta, p.nome_pac, 
										 pr.nome_prof, e.nome_espec, pl.nome_plano
								  FROM  item_consulta AS ic
								  INNER JOIN (horarios_profissionais as hp, horarios as h,
								  dias_semana as ds, periodo_consulta as pc, consultas as c,
								  pacientes as p, profissionais as pr, especialidades as e,
								  plano_procedimento as pp, planos as pl)
								  ON (ic.horarios_id = hp.id AND hp.horarios_id = h.id AND ic.dia_semana_id = ds.id AND ic.periodo_consulta_id = pc.id AND
								  ic.consultas_id = c.id AND c.pacientes_id = p.id AND c.profissionais_id = pr.id AND pr.especialidades_id = e.id AND
								  c.plano_procedimento_id = pp.id AND pp.planos_id = pl.id)
								  WHERE month(pc.data) = $periodo_consulta_id AND 
								        pr.id = $profissional_id AND ic.status = 1")
						->result();
	}

	public function getCalendarioProfissionais($periodo_consulta_id, $profissional_id) {
		return $this->db->query(" SELECT x.id, ifnull(x.horario_id, hr.id) as horario_id, 
									       ifnull(x.desc_horario, hr.desc_horario) as desc_horario,
											 ifnull(x.dia_semana_id, ds.id) as dia_semana_id,
											 ifnull(x.desc_dia_semana, ds.desc_dia_semana) as desc_dia_semana,
											 pr.id as profissional_id,
											 pr.nome_prof,
											 x.data,
											 x.status, x.cod_consulta, x.nome_pac,
											 x.nome_espec, x.nome_plano
										FROM (
												SELECT ic.id, ic.horario_id, ic.desc_horario, ic.dia_semana_id, ic.desc_dia_semana, ic.data,
														ic.status, ic.cod_consulta, ic.nome_pac, ic.profissional_id, 
														ic.nome_prof, ic.nome_espec, ic.nome_plano
												  FROM vw_item_consulta as ic	 
												  WHERE MONTH(data) = '$periodo_consulta_id'
											) AS x
									  RIGHT JOIN (horarios as hr, dias_semana as ds, profissionais as pr)
									  ON (x.horario_id = hr.id AND x.dia_semana_id = ds.id 
									      AND x.profissional_id = pr.id)
									  WHERE pr.id = '$profissional_id'
									  ORDER BY pr.id ASC, ds.id ASC, hr.id ASC ")
						->result();
		
	}

	public function get_status() {
		return $this->db->query(" SELECT * 
								  FROM status ")
						->result();
	}
}