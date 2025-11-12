--
-- PostgreSQL database dump
--

-- Dumped from database version 16.9 (Debian 16.9-1.pgdg120+1)
-- Dumped by pg_dump version 16.9 (Debian 16.9-1.pgdg120+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: function_empleado_pedido(bigint, date, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.function_empleado_pedido(idrestaurante bigint, fechaini date, fechafin date) RETURNS TABLE(nombre_usuario character varying, perfil text, pedidos bigint)
    LANGUAGE plpgsql
    AS $$
begin
	return query 
		select c.nombre_usuario, 'Cajero' as perfil, count(*) as pedidos from cajeros as c
		inner join cajas as j on j.id_caja = c.id_caja
		inner join sucursals as s on s.id_sucursal = c.id_sucursal
		inner join venta_productos as v on v.id_cajero = c.id_cajero
		inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
		where s.id_restaurant = idRestaurante and c.deleted_at isNull and h.fecha between fechaIni and fechaFin
		group by c.nombre_usuario
		union
		select m.nombre_usuario, 'Mozo' as perfil, count(*) as pedidos from mozos as m
		inner join sucursals as s on s.id_sucursal = m.id_sucursal
		inner join venta_productos as v on v.id_mozo = m.id_mozo
		inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
		where s.id_restaurant = idRestaurante and m.deleted_at isNull and h.fecha between fechaIni and fechaFin
		group by m.nombre_usuario
		order by pedidos desc, perfil asc;
end;
$$;


ALTER FUNCTION public.function_empleado_pedido(idrestaurante bigint, fechaini date, fechafin date) OWNER TO postgres;

--
-- Name: function_fecha_importe(bigint, date, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.function_fecha_importe(idrestaurante bigint, fechaini date, fechafin date) RETURNS TABLE(fecha date, importe numeric)
    LANGUAGE plpgsql
    AS $$
begin
	return query
	select 
		h.fecha, 
		sum(p.importe)
	from producto_vendidos as p
	inner join venta_productos as v on v.id_venta_producto = p.id_venta_producto
	inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
	inner join cajas as c on c.id_caja = h.id_caja
	inner join sucursals as s on s.id_sucursal = c.id_sucursal
	where s.id_restaurant = idRestaurante and h.fecha between fechaIni and fechaFin and v.estado_venta = 0
	group by h.fecha
	order by h.fecha asc;
end;
$$;


ALTER FUNCTION public.function_fecha_importe(idrestaurante bigint, fechaini date, fechafin date) OWNER TO postgres;

--
-- Name: function_fecha_pedidos(bigint, date, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.function_fecha_pedidos(idrestaurante bigint, fechaini date, fechafin date) RETURNS TABLE(fecha date, pedidos bigint)
    LANGUAGE plpgsql
    AS $$
begin
	return query
	select h.fecha, count(*) as pedidos from venta_productos as v
	inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
	inner join cajas as c on c.id_caja = h.id_caja
	inner join sucursals as s on s.id_sucursal = c.id_sucursal
	where s.id_restaurant = idRestaurante and h.fecha between fechaIni and fechaFin
	group by h.fecha
	order by h.fecha asc;
end;
$$;


ALTER FUNCTION public.function_fecha_pedidos(idrestaurante bigint, fechaini date, fechafin date) OWNER TO postgres;

--
-- Name: function_personal(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.function_personal(idrestaurant integer) RETURNS TABLE(id_usuario bigint, nombre_usuario character varying, nombre_completo text, dni character varying, tipo_usuario character, perfil text, nombresucursal character varying, created_at timestamp without time zone, id_sucursal bigint, estado boolean)
    LANGUAGE plpgsql
    AS $$begin
	return query 
		select c.id_cajero as id_usuario,
				c.nombre_usuario as nombre_usuario, 
				concat(c.primer_nombre,' ', c.segundo_nombre,' ',c.paterno,' ',c.materno) as nombre_completo, 
				c.dni as dni, 
				c.tipo_usuario as tipo_usuario, 
				'Cajero' as perfil, 
				s.nombre as nombreSucursal, 
				c.created_at as created_at, 
				s.id_sucursal,
				c.estado
		from cajeros as c
		inner join sucursals as s on s.id_sucursal = c.id_sucursal
		where s.id_restaurant = idRestaurant and c.deleted_at is null
		union
		select m.id_mozo as id_usuario,
				m.nombre_usuario as nombre_usuario, 
				concat(m.primer_nombre,' ',m.segundo_nombre,' ',m.paterno,' ',m.materno) as nombre_completo, 
				m.dni as dni, 
				m.tipo_usuario as tipo_usuario, 
				'Mozo' as perfil, 
				s.nombre as nombreSucursal, 
				m.created_at as created_at, 
				s.id_sucursal,
				m.estado
		from mozos as m
		inner join sucursals as s on s.id_sucursal = m.id_sucursal
		where s.id_restaurant = idRestaurant and m.deleted_at is null
		union
		select n.id_cocinero as id_usuario, 
				n.nombre_usuario as nombre_usuario, 
				concat(n.primer_nombre,' ',n.segundo_nombre,' ',n.paterno,' ',n.materno) as nombre_completo, 
				n.dni as dni,
				n.tipo_usuario as tipo_usuario,
				'Cocinero' as perfil,
				s.nombre as nombreSucursal,
				n.created_at as created_at,
				s.id_sucursal,
				n.estado
		from cocineros as n
		inner join sucursals as s on s.id_sucursal = n.id_sucursal
		where s.id_restaurant = idRestaurant and n.deleted_at is null
		order by created_at desc;
end;
$$;


ALTER FUNCTION public.function_personal(idrestaurant integer) OWNER TO postgres;

--
-- Name: function_producto_cantidad(bigint, bigint, date, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.function_producto_cantidad(idrestaurante bigint, idcategoria bigint, fechaini date, fechafin date) RETURNS TABLE(nom_producto character varying, nom_categoria character varying, cantidad bigint)
    LANGUAGE plpgsql
    AS $$
begin
	if idCategoria != -1 then
		return query 	
			select p.nombre as nom_producto, ct.nombre as nom_categoria, sum(pv.cantidad) as cantidad from productos as p
			inner join categoria_productos as ct on ct.id_categoria_producto = p.id_categoria_producto
			inner join producto_vendidos as pv on pv.id_producto = p.id_producto
			inner join venta_productos as v on v.id_venta_producto = pv.id_venta_producto
			inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
			inner join cajas as c on c.id_caja = h.id_caja
			inner join sucursals as s on s.id_sucursal = c.id_sucursal
			where s.id_restaurant = idRestaurante and ct.id_categoria_producto = idCategoria and date(v.created_at) between date(fechaIni) and date(fechaFin) and v.estado_venta = 0
			group by p.nombre, ct.nombre, ct.id_categoria_producto
			order by cantidad desc;
	else
		return query 	
			select p.nombre as nom_producto, ct.nombre as nom_categoria, sum(pv.cantidad) as cantidad from productos as p
			inner join categoria_productos as ct on ct.id_categoria_producto = p.id_categoria_producto
			inner join producto_vendidos as pv on pv.id_producto = p.id_producto
			inner join venta_productos as v on v.id_venta_producto = pv.id_venta_producto
			inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
			inner join cajas as c on c.id_caja = h.id_caja
			inner join sucursals as s on s.id_sucursal = c.id_sucursal
			where s.id_restaurant = idRestaurante and date(v.created_at) between date(fechaIni) and date(fechaFin) and v.estado_venta = 0
			group by p.nombre, ct.nombre, ct.id_categoria_producto
			order by cantidad desc;
	end if;
end;
$$;


ALTER FUNCTION public.function_producto_cantidad(idrestaurante bigint, idcategoria bigint, fechaini date, fechafin date) OWNER TO postgres;

--
-- Name: function_producto_importe(bigint, bigint, date, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.function_producto_importe(idrestaurante bigint, idcategoria bigint, fechaini date, fechafin date) RETURNS TABLE(nom_producto character varying, nom_categoria character varying, importe numeric)
    LANGUAGE plpgsql
    AS $$
begin
	if idCategoria != -1 then
		return query 	
			select p.nombre as nom_producto, ct.nombre as nom_categoria, sum(pv.importe) as importe
			from productos as p
			inner join categoria_productos as ct on ct.id_categoria_producto = p.id_categoria_producto
			inner join producto_vendidos as pv on pv.id_producto = p.id_producto
			inner join venta_productos as v on v.id_venta_producto = pv.id_venta_producto
			inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
			inner join cajas as c on c.id_caja = h.id_caja
			inner join sucursals as s on s.id_sucursal = c.id_sucursal
			where s.id_restaurant = idRestaurante and ct.id_categoria_producto = idCategoria and date(v.created_at) between date(fechaIni) and date(fechaFin) and v.estado_venta = 0
			group by p.nombre, ct.nombre, ct.id_categoria_producto
			order by importe desc;
	else
		return query 	
			select p.nombre as nom_producto, ct.nombre as nom_categoria, sum(pv.importe) as importe 
			from productos as p
			inner join categoria_productos as ct on ct.id_categoria_producto = p.id_categoria_producto
			inner join producto_vendidos as pv on pv.id_producto = p.id_producto
			inner join venta_productos as v on v.id_venta_producto = pv.id_venta_producto
			inner join historial_caja as h on h.id_historial_caja = v.id_historial_caja
			inner join cajas as c on c.id_caja = h.id_caja
			inner join sucursals as s on s.id_sucursal = c.id_sucursal
			where s.id_restaurant = idRestaurante and date(v.created_at) between date(fechaIni) and date(fechaFin) and v.estado_venta = 0
			group by p.nombre, ct.nombre, ct.id_categoria_producto
			order by importe desc;
	end if;
end;
$$;


ALTER FUNCTION public.function_producto_importe(idrestaurante bigint, idcategoria bigint, fechaini date, fechafin date) OWNER TO postgres;

--
-- Name: registraproductosfunction(text, bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.registraproductosfunction(cad text, id_venta_producto bigint) RETURNS integer
    LANGUAGE plpgsql
    AS $$
declare
	nro_elementos integer;
	row_cad text;
	col_cad text;
	i integer;
	j integer;
	
BEGIN
	i = 1;
    nro_elementos = (select array_length(regexp_split_to_array(cad, ':'),1));
    WHILE i <= (select nro_elementos) LOOP
		row_cad = (SELECT split_part(cad, ':', i));
		raise notice '%',row_cad;
		raise notice '-> %', (SELECT split_part(row_cad, '|', 1));--cod
		raise notice '-> %', (SELECT split_part(row_cad, '|', 2));--cant
		raise notice '-> %', (SELECT split_part(row_cad, '|', 3));--p_unit
		raise notice '-> %', (SELECT split_part(row_cad, '|', 4));--importe
		raise notice '-> %', (SELECT split_part(row_cad, '|', 5));--nota
		raise notice '-> %', (SELECT split_part(row_cad, '|', 6));--p_base
		raise notice '-> %', (SELECT split_part(row_cad, '|', 7));--importe_base
		insert into producto_vendidos
			(
				id_producto,
				cantidad,
				p_unit,
				importe,
				nota,
				id_venta_producto,
				created_at,
				precio_base,
				importe_base
			)
			values (
				CAST(split_part(row_cad, '|', 1) as bigint),--cod
				CAST(split_part(row_cad, '|', 2) as int),--cant
				CAST(split_part(row_cad, '|', 3) as numeric(9, 4)),--p_unit
				CAST(split_part(row_cad, '|', 4) as numeric(9, 4)),--importe
				(SELECT split_part(row_cad, '|', 5)),--nota
				id_venta_producto,
				now(),
				CAST(split_part(row_cad, '|', 6) as numeric(9, 4)),--precio_base
				CAST(split_part(row_cad, '|', 7) as numeric(9, 4))--importe_base
			);
		
		--raise notice '% %', suma, i;
		i = i+1;
	END LOOP;
    RETURN i-1;
END;          
$$;


ALTER FUNCTION public.registraproductosfunction(cad text, id_venta_producto bigint) OWNER TO postgres;

--
-- Name: reporte_ventas_por_rango_fecha_detallegeneral(bigint, date, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.reporte_ventas_por_rango_fecha_detallegeneral(idrestaurante bigint, fecha_inicio date, fecha_fin date) RETURNS TABLE(fecha date, cantidad numeric, total_efectivo numeric, total_tarjeta numeric, total_qr numeric, total_ventas numeric)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        t.fecha,
        SUM(t.cantidad) AS cantidad,
        SUM(t.total_efectivo) AS total_efectivo,
        SUM(t.total_tarjeta) AS total_tarjeta,
        SUM(t.total_qr) AS total_qr,
        SUM(t.total_efectivo + t.total_tarjeta + t.total_qr) AS total_ventas
    FROM (
        -- efectivo
        SELECT
            DATE(vp.created_at) AS fecha,
            COUNT(*) AS cantidad,
            SUM(p.importe) AS total_efectivo,
            0::NUMERIC AS total_tarjeta,
            0::NUMERIC AS total_qr
        FROM venta_productos vp
        JOIN pagos p ON p.id_venta_producto = vp.id_venta_producto
		JOIN historial_caja as h on h.id_historial_caja = vp.id_historial_caja
		JOIN cajas as c on c.id_caja = h.id_caja
		JOIN sucursals as s on s.id_sucursal = c.id_sucursal
        WHERE s.id_restaurant = idRestaurante and DATE(vp.created_at) BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)
          AND p.tipo_pago = 0 and vp.estado_venta = 0
        GROUP BY DATE(vp.created_at)

        UNION ALL

        -- tarjeta
        SELECT
            DATE(vp.created_at) AS fecha,
            COUNT(*) AS cantidad,
            0::NUMERIC AS total_efectivo,
            SUM(p.importe) AS total_tarjeta,
            0::NUMERIC AS total_qr
        FROM venta_productos vp
        JOIN pagos p ON p.id_venta_producto = vp.id_venta_producto
       	JOIN historial_caja as h on h.id_historial_caja = vp.id_historial_caja
		JOIN cajas as c on c.id_caja = h.id_caja
		JOIN sucursals as s on s.id_sucursal = c.id_sucursal
        WHERE s.id_restaurant = idRestaurante and DATE(vp.created_at) BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)
          AND p.tipo_pago = 1 and vp.estado_venta = 0
        GROUP BY DATE(vp.created_at)

        UNION ALL

        -- qr
        SELECT
            DATE(vp.created_at) AS fecha,
            COUNT(*) AS cantidad,
            0::NUMERIC AS total_efectivo,
            0::NUMERIC AS total_tarjeta,
            SUM(p.importe) AS total_qr
        FROM venta_productos vp
        JOIN pagos p ON p.id_venta_producto = vp.id_venta_producto
       	JOIN historial_caja as h on h.id_historial_caja = vp.id_historial_caja
		JOIN cajas as c on c.id_caja = h.id_caja
		JOIN sucursals as s on s.id_sucursal = c.id_sucursal
        WHERE s.id_restaurant = idRestaurante and DATE(vp.created_at) BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)
          AND p.tipo_pago = 2 and vp.estado_venta = 0
        GROUP BY DATE(vp.created_at)
    ) t
    GROUP BY t.fecha
    ORDER BY t.fecha DESC;
END;
$$;


ALTER FUNCTION public.reporte_ventas_por_rango_fecha_detallegeneral(idrestaurante bigint, fecha_inicio date, fecha_fin date) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id_usuario bigint NOT NULL,
    primer_nombre character varying(100),
    paterno character varying(250),
    materno character varying(250),
    dni character varying(25),
    direccion text,
    nombre_usuario character varying(25),
    email character varying(250),
    password character varying(500),
    fecha_nac date,
    sexo boolean,
    nombre_fotoperfil character varying(150),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    tipo_usuario character(1),
    api_token character varying(60),
    segundo_nombre character varying(100),
    celular character varying(20),
    telefono character varying(20),
    deleted_at timestamp without time zone,
    estado boolean
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: administradors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.administradors (
    id_administrador bigint NOT NULL,
    id_restaurant bigint NOT NULL,
    id_superadministrador bigint NOT NULL
)
INHERITS (public.users);


ALTER TABLE public.administradors OWNER TO postgres;

--
-- Name: administradors_id_administrador_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.administradors_id_administrador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.administradors_id_administrador_seq OWNER TO postgres;

--
-- Name: administradors_id_administrador_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.administradors_id_administrador_seq OWNED BY public.administradors.id_administrador;


--
-- Name: cajas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cajas (
    id_caja bigint NOT NULL,
    nombre character varying(20) NOT NULL,
    descripcion text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    id_administrador bigint,
    id_sucursal bigint,
    deleted_at timestamp without time zone,
    id_superadministrador bigint
);


ALTER TABLE public.cajas OWNER TO postgres;

--
-- Name: cajas_id_caja_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cajas_id_caja_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cajas_id_caja_seq OWNER TO postgres;

--
-- Name: cajas_id_caja_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cajas_id_caja_seq OWNED BY public.cajas.id_caja;


--
-- Name: cajas_id_superadministrador_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cajas_id_superadministrador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cajas_id_superadministrador_seq OWNER TO postgres;

--
-- Name: cajas_id_superadministrador_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cajas_id_superadministrador_seq OWNED BY public.cajas.id_superadministrador;


--
-- Name: cajeros; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cajeros (
    id_cajero bigint NOT NULL,
    sueldo numeric(9,2),
    fecha_inicio date,
    id_administrador bigint,
    id_caja bigint,
    id_superadministrador bigint
)
INHERITS (public.users);


ALTER TABLE public.cajeros OWNER TO postgres;

--
-- Name: cajeros_id_superadministrador_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cajeros_id_superadministrador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cajeros_id_superadministrador_seq OWNER TO postgres;

--
-- Name: cajeros_id_superadministrador_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cajeros_id_superadministrador_seq OWNED BY public.cajeros.id_superadministrador;


--
-- Name: categoria_productos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categoria_productos (
    id_categoria_producto bigint NOT NULL,
    nombre character varying(50) NOT NULL,
    descripcion text,
    fecha_inicio date,
    id_restaurant bigint NOT NULL,
    id_administrador bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    id_cajero bigint
);


ALTER TABLE public.categoria_productos OWNER TO postgres;

--
-- Name: categoria_productos_id_categoria_producto_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categoria_productos_id_categoria_producto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categoria_productos_id_categoria_producto_seq OWNER TO postgres;

--
-- Name: categoria_productos_id_categoria_producto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categoria_productos_id_categoria_producto_seq OWNED BY public.categoria_productos.id_categoria_producto;


--
-- Name: clientes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.clientes (
    id_cliente bigint NOT NULL,
    nombre_completo character varying(100),
    dni integer,
    id_cajero bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    id_mozo bigint,
    id_restaurant bigint
);


ALTER TABLE public.clientes OWNER TO postgres;

--
-- Name: clientes_id_cliente_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.clientes_id_cliente_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.clientes_id_cliente_seq OWNER TO postgres;

--
-- Name: clientes_id_cliente_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.clientes_id_cliente_seq OWNED BY public.clientes.id_cliente;


--
-- Name: cocineros; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cocineros (
    id_cocinero bigint NOT NULL,
    sueldo numeric(9,2),
    fecha_inicio date,
    id_sucursal bigint NOT NULL,
    id_administrador bigint NOT NULL
)
INHERITS (public.users);


ALTER TABLE public.cocineros OWNER TO postgres;

--
-- Name: cocineros_id_cocinero_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cocineros_id_cocinero_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cocineros_id_cocinero_seq OWNER TO postgres;

--
-- Name: cocineros_id_cocinero_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cocineros_id_cocinero_seq OWNED BY public.cocineros.id_cocinero;


--
-- Name: diccionario_datos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.diccionario_datos (
    id_diccionario integer NOT NULL,
    tabla character varying(100) NOT NULL,
    campo character varying(50) NOT NULL,
    codigo integer NOT NULL,
    descripcion character varying(100) NOT NULL,
    valor_extra character varying(100),
    orden integer,
    activo boolean DEFAULT true NOT NULL,
    fecha_creacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fecha_actualizacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.diccionario_datos OWNER TO postgres;

--
-- Name: diccionario_datos_id_diccionario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.diccionario_datos_id_diccionario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.diccionario_datos_id_diccionario_seq OWNER TO postgres;

--
-- Name: diccionario_datos_id_diccionario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.diccionario_datos_id_diccionario_seq OWNED BY public.diccionario_datos.id_diccionario;


--
-- Name: empleados_id_empleado_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.empleados_id_empleado_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.empleados_id_empleado_seq OWNER TO postgres;

--
-- Name: empleados_id_empleado_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.empleados_id_empleado_seq OWNED BY public.cajeros.id_cajero;


--
-- Name: historial_caja; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.historial_caja (
    id_historial_caja bigint NOT NULL,
    monto_inicial numeric(10,2) NOT NULL,
    monto numeric(10,2) NOT NULL,
    fecha date NOT NULL,
    estado boolean NOT NULL,
    id_caja bigint NOT NULL,
    id_administrador bigint,
    id_cajero bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.historial_caja OWNER TO postgres;

--
-- Name: historial_caja_id_historial_caja_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.historial_caja_id_historial_caja_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.historial_caja_id_historial_caja_seq OWNER TO postgres;

--
-- Name: historial_caja_id_historial_caja_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.historial_caja_id_historial_caja_seq OWNED BY public.historial_caja.id_historial_caja;


--
-- Name: mozos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mozos (
    id_mozo bigint NOT NULL,
    sueldo numeric(9,2),
    fecha_inicio date,
    id_sucursal bigint NOT NULL,
    id_administrador bigint NOT NULL
)
INHERITS (public.users);


ALTER TABLE public.mozos OWNER TO postgres;

--
-- Name: mozos_id_mozo_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mozos_id_mozo_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mozos_id_mozo_seq OWNER TO postgres;

--
-- Name: mozos_id_mozo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mozos_id_mozo_seq OWNED BY public.mozos.id_mozo;


--
-- Name: pagos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pagos (
    id_pago bigint NOT NULL,
    importe numeric(9,2),
    cambio numeric(9,2),
    id_venta_producto bigint NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    id_cajero bigint,
    tipo_pago integer,
    efectivo numeric(9,2),
    importe_base numeric(9,2),
    tipo_servicio integer
);


ALTER TABLE public.pagos OWNER TO postgres;

--
-- Name: pagos_id_pago_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pagos_id_pago_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pagos_id_pago_seq OWNER TO postgres;

--
-- Name: pagos_id_pago_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pagos_id_pago_seq OWNED BY public.pagos.id_pago;


--
-- Name: perfilimagens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.perfilimagens (
    id_perfilimagen bigint NOT NULL,
    nombre character varying(250) NOT NULL,
    id_administrador bigint,
    id_mozo bigint,
    id_cajero bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.perfilimagens OWNER TO postgres;

--
-- Name: perfilimagens_id_perfilimagen_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.perfilimagens_id_perfilimagen_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.perfilimagens_id_perfilimagen_seq OWNER TO postgres;

--
-- Name: perfilimagens_id_perfilimagen_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.perfilimagens_id_perfilimagen_seq OWNED BY public.perfilimagens.id_perfilimagen;


--
-- Name: plan_de_pagos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.plan_de_pagos (
    id_planpago bigint NOT NULL,
    cant_pedidos integer,
    cant_mozos integer,
    cant_cajas integer,
    cant_cajeros integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    id_suscripcion bigint NOT NULL,
    cant_cocineros integer
);


ALTER TABLE public.plan_de_pagos OWNER TO postgres;

--
-- Name: plan_de_pagos_id_planpago_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.plan_de_pagos_id_planpago_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.plan_de_pagos_id_planpago_seq OWNER TO postgres;

--
-- Name: plan_de_pagos_id_planpago_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.plan_de_pagos_id_planpago_seq OWNED BY public.plan_de_pagos.id_planpago;


--
-- Name: producto_vendidos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.producto_vendidos (
    id_producto_vendido bigint NOT NULL,
    cantidad integer,
    importe numeric(9,2),
    id_producto bigint NOT NULL,
    id_venta_producto bigint NOT NULL,
    nota text,
    p_unit numeric(9,2),
    created_at timestamp without time zone,
    precio_base numeric(9,2),
    importe_base numeric(9,2)
);


ALTER TABLE public.producto_vendidos OWNER TO postgres;

--
-- Name: producto_vendidos_id_producto_vendido_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.producto_vendidos_id_producto_vendido_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.producto_vendidos_id_producto_vendido_seq OWNER TO postgres;

--
-- Name: producto_vendidos_id_producto_vendido_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.producto_vendidos_id_producto_vendido_seq OWNED BY public.producto_vendidos.id_producto_vendido;


--
-- Name: productos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.productos (
    id_producto bigint NOT NULL,
    nombre character varying(50) NOT NULL,
    descripcion text,
    id_categoria_producto bigint NOT NULL,
    id_administrador bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    precio numeric(9,2),
    deleted_at timestamp without time zone,
    producto_image character varying(250),
    id_cajero bigint,
    precio_base numeric(9,2)
);


ALTER TABLE public.productos OWNER TO postgres;

--
-- Name: productos_id_producto_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.productos_id_producto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.productos_id_producto_seq OWNER TO postgres;

--
-- Name: productos_id_producto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.productos_id_producto_seq OWNED BY public.productos.id_producto;


--
-- Name: restaurants; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.restaurants (
    id_restaurant bigint NOT NULL,
    nombre character varying(50) NOT NULL,
    estado boolean NOT NULL,
    descripcion text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    id_superadministrador bigint NOT NULL,
    observacion text,
    id_suscripcion bigint,
    tipo_moneda character varying(10),
    identificacion character varying(10)
);


ALTER TABLE public.restaurants OWNER TO postgres;

--
-- Name: restaurants_id_restaurant_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.restaurants_id_restaurant_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.restaurants_id_restaurant_seq OWNER TO postgres;

--
-- Name: restaurants_id_restaurant_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.restaurants_id_restaurant_seq OWNED BY public.restaurants.id_restaurant;


--
-- Name: sucursals; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sucursals (
    id_sucursal bigint NOT NULL,
    nombre character varying(20) NOT NULL,
    direccion text,
    descripcion text,
    id_restaurant bigint NOT NULL,
    id_superadministrador bigint NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    ciudad character varying(20),
    pais character varying(20),
    telefono character varying(20),
    celular character varying(20)
);


ALTER TABLE public.sucursals OWNER TO postgres;

--
-- Name: sucursals_id_sucursal_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sucursals_id_sucursal_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sucursals_id_sucursal_seq OWNER TO postgres;

--
-- Name: sucursals_id_sucursal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sucursals_id_sucursal_seq OWNED BY public.sucursals.id_sucursal;


--
-- Name: superadministradors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.superadministradors (
    id_superadministrador bigint NOT NULL,
    nombre_usuario character varying(250) NOT NULL,
    password character varying(250) NOT NULL,
    created_at timestamp without time zone DEFAULT (now())::timestamp(3) without time zone,
    updated_at timestamp without time zone DEFAULT (now())::timestamp(3) without time zone
);


ALTER TABLE public.superadministradors OWNER TO postgres;

--
-- Name: superadministradors_id_superadministrador_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.superadministradors_id_superadministrador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.superadministradors_id_superadministrador_seq OWNER TO postgres;

--
-- Name: superadministradors_id_superadministrador_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.superadministradors_id_superadministrador_seq OWNED BY public.superadministradors.id_superadministrador;


--
-- Name: suscripcions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.suscripcions (
    id_suscripcion bigint NOT NULL,
    tipo_suscripcion character varying(20) NOT NULL,
    observacion text,
    precio_anual numeric(9,2),
    precio_mensual numeric(9,2),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    id_superadministrador bigint NOT NULL
);


ALTER TABLE public.suscripcions OWNER TO postgres;

--
-- Name: suscripcions_id_suscripcion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.suscripcions_id_suscripcion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.suscripcions_id_suscripcion_seq OWNER TO postgres;

--
-- Name: suscripcions_id_suscripcion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.suscripcions_id_suscripcion_seq OWNED BY public.suscripcions.id_suscripcion;


--
-- Name: users_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_usuario_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_usuario_seq OWNER TO postgres;

--
-- Name: users_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_usuario_seq OWNED BY public.users.id_usuario;


--
-- Name: venta_productos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.venta_productos (
    id_venta_producto bigint NOT NULL,
    nro_venta integer,
    estado_venta integer,
    id_cliente bigint,
    id_cajero bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    id_sucursal bigint,
    id_mozo bigint,
    id_historial_caja bigint,
    estado_atendido boolean,
    id_cocinero bigint
);


ALTER TABLE public.venta_productos OWNER TO postgres;

--
-- Name: venta_productos_id_venta_producto_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.venta_productos_id_venta_producto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.venta_productos_id_venta_producto_seq OWNER TO postgres;

--
-- Name: venta_productos_id_venta_producto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.venta_productos_id_venta_producto_seq OWNED BY public.venta_productos.id_venta_producto;


--
-- Name: administradors id_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.administradors ALTER COLUMN id_usuario SET DEFAULT nextval('public.users_id_usuario_seq'::regclass);


--
-- Name: administradors id_administrador; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.administradors ALTER COLUMN id_administrador SET DEFAULT nextval('public.administradors_id_administrador_seq'::regclass);


--
-- Name: cajas id_caja; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajas ALTER COLUMN id_caja SET DEFAULT nextval('public.cajas_id_caja_seq'::regclass);


--
-- Name: cajas id_superadministrador; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajas ALTER COLUMN id_superadministrador SET DEFAULT nextval('public.cajas_id_superadministrador_seq'::regclass);


--
-- Name: cajeros id_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajeros ALTER COLUMN id_usuario SET DEFAULT nextval('public.users_id_usuario_seq'::regclass);


--
-- Name: cajeros id_cajero; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajeros ALTER COLUMN id_cajero SET DEFAULT nextval('public.empleados_id_empleado_seq'::regclass);


--
-- Name: cajeros id_superadministrador; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajeros ALTER COLUMN id_superadministrador SET DEFAULT nextval('public.cajeros_id_superadministrador_seq'::regclass);


--
-- Name: categoria_productos id_categoria_producto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria_productos ALTER COLUMN id_categoria_producto SET DEFAULT nextval('public.categoria_productos_id_categoria_producto_seq'::regclass);


--
-- Name: clientes id_cliente; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clientes ALTER COLUMN id_cliente SET DEFAULT nextval('public.clientes_id_cliente_seq'::regclass);


--
-- Name: cocineros id_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cocineros ALTER COLUMN id_usuario SET DEFAULT nextval('public.users_id_usuario_seq'::regclass);


--
-- Name: cocineros id_cocinero; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cocineros ALTER COLUMN id_cocinero SET DEFAULT nextval('public.cocineros_id_cocinero_seq'::regclass);


--
-- Name: diccionario_datos id_diccionario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diccionario_datos ALTER COLUMN id_diccionario SET DEFAULT nextval('public.diccionario_datos_id_diccionario_seq'::regclass);


--
-- Name: historial_caja id_historial_caja; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_caja ALTER COLUMN id_historial_caja SET DEFAULT nextval('public.historial_caja_id_historial_caja_seq'::regclass);


--
-- Name: mozos id_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mozos ALTER COLUMN id_usuario SET DEFAULT nextval('public.users_id_usuario_seq'::regclass);


--
-- Name: mozos id_mozo; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mozos ALTER COLUMN id_mozo SET DEFAULT nextval('public.mozos_id_mozo_seq'::regclass);


--
-- Name: pagos id_pago; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos ALTER COLUMN id_pago SET DEFAULT nextval('public.pagos_id_pago_seq'::regclass);


--
-- Name: perfilimagens id_perfilimagen; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfilimagens ALTER COLUMN id_perfilimagen SET DEFAULT nextval('public.perfilimagens_id_perfilimagen_seq'::regclass);


--
-- Name: plan_de_pagos id_planpago; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.plan_de_pagos ALTER COLUMN id_planpago SET DEFAULT nextval('public.plan_de_pagos_id_planpago_seq'::regclass);


--
-- Name: producto_vendidos id_producto_vendido; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto_vendidos ALTER COLUMN id_producto_vendido SET DEFAULT nextval('public.producto_vendidos_id_producto_vendido_seq'::regclass);


--
-- Name: productos id_producto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.productos ALTER COLUMN id_producto SET DEFAULT nextval('public.productos_id_producto_seq'::regclass);


--
-- Name: restaurants id_restaurant; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants ALTER COLUMN id_restaurant SET DEFAULT nextval('public.restaurants_id_restaurant_seq'::regclass);


--
-- Name: sucursals id_sucursal; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sucursals ALTER COLUMN id_sucursal SET DEFAULT nextval('public.sucursals_id_sucursal_seq'::regclass);


--
-- Name: superadministradors id_superadministrador; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.superadministradors ALTER COLUMN id_superadministrador SET DEFAULT nextval('public.superadministradors_id_superadministrador_seq'::regclass);


--
-- Name: suscripcions id_suscripcion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suscripcions ALTER COLUMN id_suscripcion SET DEFAULT nextval('public.suscripcions_id_suscripcion_seq'::regclass);


--
-- Name: users id_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id_usuario SET DEFAULT nextval('public.users_id_usuario_seq'::regclass);


--
-- Name: venta_productos id_venta_producto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos ALTER COLUMN id_venta_producto SET DEFAULT nextval('public.venta_productos_id_venta_producto_seq'::regclass);


--
-- Data for Name: administradors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.administradors (id_usuario, primer_nombre, paterno, materno, dni, direccion, nombre_usuario, email, password, fecha_nac, sexo, nombre_fotoperfil, created_at, updated_at, tipo_usuario, api_token, segundo_nombre, celular, telefono, deleted_at, estado, id_administrador, id_restaurant, id_superadministrador) FROM stdin;
\.


--
-- Data for Name: cajas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cajas (id_caja, nombre, descripcion, created_at, updated_at, id_administrador, id_sucursal, deleted_at, id_superadministrador) FROM stdin;
\.


--
-- Data for Name: cajeros; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cajeros (id_usuario, primer_nombre, paterno, materno, dni, direccion, nombre_usuario, email, password, fecha_nac, sexo, nombre_fotoperfil, created_at, updated_at, tipo_usuario, api_token, segundo_nombre, celular, telefono, deleted_at, estado, id_cajero, sueldo, fecha_inicio, id_administrador, id_caja, id_superadministrador) FROM stdin;
\.


--
-- Data for Name: categoria_productos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categoria_productos (id_categoria_producto, nombre, descripcion, fecha_inicio, id_restaurant, id_administrador, created_at, updated_at, deleted_at, id_cajero) FROM stdin;
\.


--
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.clientes (id_cliente, nombre_completo, dni, id_cajero, created_at, updated_at, id_mozo, id_restaurant) FROM stdin;
\.


--
-- Data for Name: cocineros; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cocineros (id_usuario, primer_nombre, paterno, materno, dni, direccion, nombre_usuario, email, password, fecha_nac, sexo, nombre_fotoperfil, created_at, updated_at, tipo_usuario, api_token, segundo_nombre, celular, telefono, deleted_at, estado, id_cocinero, sueldo, fecha_inicio, id_sucursal, id_administrador) FROM stdin;
\.


--
-- Data for Name: diccionario_datos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.diccionario_datos (id_diccionario, tabla, campo, codigo, descripcion, valor_extra, orden, activo, fecha_creacion, fecha_actualizacion) FROM stdin;
4	pagos	tipo_servicio	0	mesa	verde	1	t	2025-09-18 11:46:09.618	2025-09-18 11:46:09.618
5	pagos	tipo_servicio	1	delivery	rojo	2	t	2025-09-18 11:46:09.618	2025-09-18 11:46:09.618
7	pagos	tipo_pago	0	efectivo	verde	1	t	2025-09-18 11:46:13.735	2025-09-18 11:46:13.735
8	pagos	tipo_pago	1	tarjeta	rojo	2	t	2025-09-18 11:46:13.735	2025-09-18 11:46:13.735
1	venta_productos	estado_venta	0	Pagado	verde	1	t	2025-09-12 13:23:07.474	2025-09-12 13:23:07.474
2	venta_productos	estado_venta	1	Cancelado	rojo	2	t	2025-09-12 13:23:07.474	2025-09-12 13:23:07.474
3	venta_productos	estado_venta	2	Pendiente	amarillo	3	t	2025-09-12 13:23:07.474	2025-09-12 13:23:07.474
6	pagos	tipo_servicio	2	para llevar	amarillo	3	t	2025-09-18 11:46:09.618	2025-09-18 11:46:09.618
9	pagos	tipo_pago	2	pago qr	amarillo	3	t	2025-09-18 11:46:13.735	2025-09-18 11:46:13.735
\.


--
-- Data for Name: historial_caja; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.historial_caja (id_historial_caja, monto_inicial, monto, fecha, estado, id_caja, id_administrador, id_cajero, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mozos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.mozos (id_usuario, primer_nombre, paterno, materno, dni, direccion, nombre_usuario, email, password, fecha_nac, sexo, nombre_fotoperfil, created_at, updated_at, tipo_usuario, api_token, segundo_nombre, celular, telefono, deleted_at, estado, id_mozo, sueldo, fecha_inicio, id_sucursal, id_administrador) FROM stdin;
\.


--
-- Data for Name: pagos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pagos (id_pago, importe, cambio, id_venta_producto, created_at, updated_at, id_cajero, tipo_pago, efectivo, importe_base, tipo_servicio) FROM stdin;
\.


--
-- Data for Name: perfilimagens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.perfilimagens (id_perfilimagen, nombre, id_administrador, id_mozo, id_cajero, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: plan_de_pagos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.plan_de_pagos (id_planpago, cant_pedidos, cant_mozos, cant_cajas, cant_cajeros, created_at, updated_at, id_suscripcion, cant_cocineros) FROM stdin;
\.


--
-- Data for Name: producto_vendidos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.producto_vendidos (id_producto_vendido, cantidad, importe, id_producto, id_venta_producto, nota, p_unit, created_at, precio_base, importe_base) FROM stdin;
\.


--
-- Data for Name: productos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.productos (id_producto, nombre, descripcion, id_categoria_producto, id_administrador, created_at, updated_at, precio, deleted_at, producto_image, id_cajero, precio_base) FROM stdin;
\.


--
-- Data for Name: restaurants; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.restaurants (id_restaurant, nombre, estado, descripcion, created_at, updated_at, id_superadministrador, observacion, id_suscripcion, tipo_moneda, identificacion) FROM stdin;
\.


--
-- Data for Name: sucursals; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sucursals (id_sucursal, nombre, direccion, descripcion, id_restaurant, id_superadministrador, created_at, updated_at, ciudad, pais, telefono, celular) FROM stdin;
\.


--
-- Data for Name: superadministradors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.superadministradors (id_superadministrador, nombre_usuario, password, created_at, updated_at) FROM stdin;
1	thebossdontcommit	$2y$10$1DAgitcyFM9BPp1gJm4HeeR3N/CgqtBZ/4cB/TC0DDaXhXitvfUrm	2025-09-22 00:00:00	2025-09-22 00:00:00
\.


--
-- Data for Name: suscripcions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.suscripcions (id_suscripcion, tipo_suscripcion, observacion, precio_anual, precio_mensual, created_at, updated_at, id_superadministrador) FROM stdin;
1	Basico	Un mes de prueba gratis	\N	100.00	2025-09-22 00:00:00	2025-09-22 00:00:00	1
2	Estandar	Tiene todas las caracteristicas excepto los reportes del últimos año	\N	150.00	2025-09-22 00:00:00	2025-09-22 00:00:00	1
3	Pro	Tiene todas las caracteristicas	\N	200.00	2025-09-22 00:00:00	2025-09-22 00:00:00	1
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id_usuario, primer_nombre, paterno, materno, dni, direccion, nombre_usuario, email, password, fecha_nac, sexo, nombre_fotoperfil, created_at, updated_at, tipo_usuario, api_token, segundo_nombre, celular, telefono, deleted_at, estado) FROM stdin;
\.


--
-- Data for Name: venta_productos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.venta_productos (id_venta_producto, nro_venta, estado_venta, id_cliente, id_cajero, created_at, updated_at, id_sucursal, id_mozo, id_historial_caja, estado_atendido, id_cocinero) FROM stdin;
\.


--
-- Name: administradors_id_administrador_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.administradors_id_administrador_seq', 74, true);


--
-- Name: cajas_id_caja_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cajas_id_caja_seq', 49, true);


--
-- Name: cajas_id_superadministrador_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cajas_id_superadministrador_seq', 1, true);


--
-- Name: cajeros_id_superadministrador_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cajeros_id_superadministrador_seq', 1, true);


--
-- Name: categoria_productos_id_categoria_producto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categoria_productos_id_categoria_producto_seq', 51, true);


--
-- Name: clientes_id_cliente_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.clientes_id_cliente_seq', 171, true);


--
-- Name: cocineros_id_cocinero_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cocineros_id_cocinero_seq', 4, true);


--
-- Name: diccionario_datos_id_diccionario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.diccionario_datos_id_diccionario_seq', 9, true);


--
-- Name: empleados_id_empleado_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.empleados_id_empleado_seq', 35, true);


--
-- Name: historial_caja_id_historial_caja_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.historial_caja_id_historial_caja_seq', 64, true);


--
-- Name: mozos_id_mozo_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mozos_id_mozo_seq', 15, true);


--
-- Name: pagos_id_pago_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pagos_id_pago_seq', 576, true);


--
-- Name: perfilimagens_id_perfilimagen_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.perfilimagens_id_perfilimagen_seq', 63, true);


--
-- Name: plan_de_pagos_id_planpago_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.plan_de_pagos_id_planpago_seq', 3, true);


--
-- Name: producto_vendidos_id_producto_vendido_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.producto_vendidos_id_producto_vendido_seq', 2457, true);


--
-- Name: productos_id_producto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.productos_id_producto_seq', 148, true);


--
-- Name: restaurants_id_restaurant_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.restaurants_id_restaurant_seq', 49, true);


--
-- Name: sucursals_id_sucursal_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sucursals_id_sucursal_seq', 36, true);


--
-- Name: superadministradors_id_superadministrador_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.superadministradors_id_superadministrador_seq', 2, true);


--
-- Name: suscripcions_id_suscripcion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.suscripcions_id_suscripcion_seq', 3, true);


--
-- Name: users_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_usuario_seq', 129, true);


--
-- Name: venta_productos_id_venta_producto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.venta_productos_id_venta_producto_seq', 729, true);


--
-- Name: administradors administradors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.administradors
    ADD CONSTRAINT administradors_pkey PRIMARY KEY (id_administrador);


--
-- Name: cajas cajas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajas
    ADD CONSTRAINT cajas_pkey PRIMARY KEY (id_caja);


--
-- Name: categoria_productos categoria_productos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria_productos
    ADD CONSTRAINT categoria_productos_pkey PRIMARY KEY (id_categoria_producto);


--
-- Name: clientes clientes_dni_id_restaurant_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_dni_id_restaurant_key UNIQUE (dni, id_restaurant);


--
-- Name: clientes clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (id_cliente);


--
-- Name: cocineros cocineros_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cocineros
    ADD CONSTRAINT cocineros_pkey PRIMARY KEY (id_cocinero);


--
-- Name: diccionario_datos diccionario_datos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diccionario_datos
    ADD CONSTRAINT diccionario_datos_pkey PRIMARY KEY (id_diccionario);


--
-- Name: cajeros empleados_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajeros
    ADD CONSTRAINT empleados_pkey PRIMARY KEY (id_cajero);


--
-- Name: historial_caja historial_caja_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_caja
    ADD CONSTRAINT historial_caja_pkey PRIMARY KEY (id_historial_caja);


--
-- Name: mozos mozos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mozos
    ADD CONSTRAINT mozos_pkey PRIMARY KEY (id_mozo);


--
-- Name: pagos pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_pkey PRIMARY KEY (id_pago);


--
-- Name: perfilimagens perfilimagens_nombre_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfilimagens
    ADD CONSTRAINT perfilimagens_nombre_key UNIQUE (nombre);


--
-- Name: perfilimagens perfilimagens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfilimagens
    ADD CONSTRAINT perfilimagens_pkey PRIMARY KEY (id_perfilimagen);


--
-- Name: plan_de_pagos plan_de_pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.plan_de_pagos
    ADD CONSTRAINT plan_de_pagos_pkey PRIMARY KEY (id_planpago);


--
-- Name: producto_vendidos producto_vendidos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto_vendidos
    ADD CONSTRAINT producto_vendidos_pkey PRIMARY KEY (id_producto_vendido);


--
-- Name: productos productos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_pkey PRIMARY KEY (id_producto);


--
-- Name: restaurants restaurants_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants
    ADD CONSTRAINT restaurants_nombre_unique UNIQUE (nombre);


--
-- Name: restaurants restaurants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants
    ADD CONSTRAINT restaurants_pkey PRIMARY KEY (id_restaurant);


--
-- Name: sucursals sucursals_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sucursals
    ADD CONSTRAINT sucursals_pkey PRIMARY KEY (id_sucursal);


--
-- Name: superadministradors superadministradors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.superadministradors
    ADD CONSTRAINT superadministradors_pkey PRIMARY KEY (id_superadministrador);


--
-- Name: superadministradors superadministradors_usuario_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.superadministradors
    ADD CONSTRAINT superadministradors_usuario_unique UNIQUE (nombre_usuario);


--
-- Name: suscripcions suscripcions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suscripcions
    ADD CONSTRAINT suscripcions_pkey PRIMARY KEY (id_suscripcion);


--
-- Name: users users_dni_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_dni_unique UNIQUE (dni);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_nombre_usuario_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_nombre_usuario_unique UNIQUE (nombre_usuario);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id_usuario);


--
-- Name: venta_productos venta_productos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos
    ADD CONSTRAINT venta_productos_pkey PRIMARY KEY (id_venta_producto);


--
-- Name: ux_diccionario_tabla_campo_codigo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX ux_diccionario_tabla_campo_codigo ON public.diccionario_datos USING btree (tabla, campo, codigo);


--
-- Name: administradors administradors_id_restaurant_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.administradors
    ADD CONSTRAINT administradors_id_restaurant_fkey FOREIGN KEY (id_restaurant) REFERENCES public.restaurants(id_restaurant);


--
-- Name: administradors administradors_id_superadministrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.administradors
    ADD CONSTRAINT administradors_id_superadministrador_fkey FOREIGN KEY (id_superadministrador) REFERENCES public.superadministradors(id_superadministrador);


--
-- Name: cajas cajas_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajas
    ADD CONSTRAINT cajas_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: cajas cajas_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajas
    ADD CONSTRAINT cajas_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursals(id_sucursal);


--
-- Name: cajas cajas_id_superadministrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajas
    ADD CONSTRAINT cajas_id_superadministrador_fkey FOREIGN KEY (id_superadministrador) REFERENCES public.superadministradors(id_superadministrador);


--
-- Name: cajeros cajero_id_superadministrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajeros
    ADD CONSTRAINT cajero_id_superadministrador_fkey FOREIGN KEY (id_superadministrador) REFERENCES public.superadministradors(id_superadministrador);


--
-- Name: cajeros cajeros_id_caja_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajeros
    ADD CONSTRAINT cajeros_id_caja_fkey FOREIGN KEY (id_caja) REFERENCES public.cajas(id_caja);


--
-- Name: categoria_productos categoria_productos_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria_productos
    ADD CONSTRAINT categoria_productos_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: categoria_productos categoria_productos_id_restaurant_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria_productos
    ADD CONSTRAINT categoria_productos_id_restaurant_fkey FOREIGN KEY (id_restaurant) REFERENCES public.restaurants(id_restaurant);


--
-- Name: clientes clientes_id_empleado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_id_empleado_fkey FOREIGN KEY (id_cajero) REFERENCES public.cajeros(id_cajero);


--
-- Name: clientes clientes_id_mozo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_id_mozo_fkey FOREIGN KEY (id_mozo) REFERENCES public.mozos(id_mozo);


--
-- Name: clientes clientes_id_restaurant_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_id_restaurant_fkey FOREIGN KEY (id_restaurant) REFERENCES public.restaurants(id_restaurant);


--
-- Name: cocineros cocineros_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cocineros
    ADD CONSTRAINT cocineros_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: cocineros cocineros_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cocineros
    ADD CONSTRAINT cocineros_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursals(id_sucursal);


--
-- Name: cajeros empleados_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cajeros
    ADD CONSTRAINT empleados_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: categoria_productos fk_categoria_productos_id_cajero; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria_productos
    ADD CONSTRAINT fk_categoria_productos_id_cajero FOREIGN KEY (id_cajero) REFERENCES public.cajeros(id_cajero) ON DELETE SET NULL;


--
-- Name: productos fk_productos_id_cajero; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT fk_productos_id_cajero FOREIGN KEY (id_cajero) REFERENCES public.cajeros(id_cajero) ON DELETE SET NULL;


--
-- Name: historial_caja historial_caja_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_caja
    ADD CONSTRAINT historial_caja_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: historial_caja historial_caja_id_caja_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_caja
    ADD CONSTRAINT historial_caja_id_caja_fkey FOREIGN KEY (id_caja) REFERENCES public.cajas(id_caja);


--
-- Name: historial_caja historial_caja_id_cajero_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_caja
    ADD CONSTRAINT historial_caja_id_cajero_fkey FOREIGN KEY (id_cajero) REFERENCES public.cajeros(id_cajero);


--
-- Name: mozos mozos_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mozos
    ADD CONSTRAINT mozos_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: mozos mozos_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mozos
    ADD CONSTRAINT mozos_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursals(id_sucursal);


--
-- Name: pagos pagos_id_cajero_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_id_cajero_fkey FOREIGN KEY (id_cajero) REFERENCES public.cajeros(id_cajero);


--
-- Name: pagos pagos_id_venta_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_id_venta_producto_fkey FOREIGN KEY (id_venta_producto) REFERENCES public.venta_productos(id_venta_producto);


--
-- Name: perfilimagens perfilimagens_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfilimagens
    ADD CONSTRAINT perfilimagens_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: perfilimagens perfilimagens_id_cajero_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfilimagens
    ADD CONSTRAINT perfilimagens_id_cajero_fkey FOREIGN KEY (id_cajero) REFERENCES public.cajeros(id_cajero);


--
-- Name: perfilimagens perfilimagens_id_mozo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfilimagens
    ADD CONSTRAINT perfilimagens_id_mozo_fkey FOREIGN KEY (id_mozo) REFERENCES public.mozos(id_mozo);


--
-- Name: plan_de_pagos plan_de_pagos_id_suscripcion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.plan_de_pagos
    ADD CONSTRAINT plan_de_pagos_id_suscripcion_fkey FOREIGN KEY (id_suscripcion) REFERENCES public.suscripcions(id_suscripcion);


--
-- Name: producto_vendidos producto_vendidos_id_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto_vendidos
    ADD CONSTRAINT producto_vendidos_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES public.productos(id_producto);


--
-- Name: producto_vendidos producto_vendidos_id_venta_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto_vendidos
    ADD CONSTRAINT producto_vendidos_id_venta_producto_fkey FOREIGN KEY (id_venta_producto) REFERENCES public.venta_productos(id_venta_producto);


--
-- Name: productos productos_id_administrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_id_administrador_fkey FOREIGN KEY (id_administrador) REFERENCES public.administradors(id_administrador);


--
-- Name: productos productos_id_categoria_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_id_categoria_producto_fkey FOREIGN KEY (id_categoria_producto) REFERENCES public.categoria_productos(id_categoria_producto);


--
-- Name: restaurants restaurants_id_superadministrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants
    ADD CONSTRAINT restaurants_id_superadministrador_fkey FOREIGN KEY (id_superadministrador) REFERENCES public.superadministradors(id_superadministrador);


--
-- Name: restaurants restaurants_id_suscripcion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants
    ADD CONSTRAINT restaurants_id_suscripcion_fkey FOREIGN KEY (id_suscripcion) REFERENCES public.suscripcions(id_suscripcion);


--
-- Name: sucursals sucursals_id_restaurant_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sucursals
    ADD CONSTRAINT sucursals_id_restaurant_fkey FOREIGN KEY (id_restaurant) REFERENCES public.restaurants(id_restaurant);


--
-- Name: sucursals sucursals_id_superadministrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sucursals
    ADD CONSTRAINT sucursals_id_superadministrador_fkey FOREIGN KEY (id_superadministrador) REFERENCES public.superadministradors(id_superadministrador);


--
-- Name: suscripcions suscripcions_id_superadministrador_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suscripcions
    ADD CONSTRAINT suscripcions_id_superadministrador_fkey FOREIGN KEY (id_superadministrador) REFERENCES public.superadministradors(id_superadministrador);


--
-- Name: venta_productos venta_productos_id_cliente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos
    ADD CONSTRAINT venta_productos_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente);


--
-- Name: venta_productos venta_productos_id_cocinero_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos
    ADD CONSTRAINT venta_productos_id_cocinero_fkey FOREIGN KEY (id_cocinero) REFERENCES public.cocineros(id_cocinero);


--
-- Name: venta_productos venta_productos_id_empleado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos
    ADD CONSTRAINT venta_productos_id_empleado_fkey FOREIGN KEY (id_cajero) REFERENCES public.cajeros(id_cajero);


--
-- Name: venta_productos venta_productos_id_historial_caja_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos
    ADD CONSTRAINT venta_productos_id_historial_caja_fkey FOREIGN KEY (id_historial_caja) REFERENCES public.historial_caja(id_historial_caja);


--
-- Name: venta_productos venta_productos_id_mozo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos
    ADD CONSTRAINT venta_productos_id_mozo_fkey FOREIGN KEY (id_mozo) REFERENCES public.mozos(id_mozo);


--
-- Name: venta_productos venta_productos_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.venta_productos
    ADD CONSTRAINT venta_productos_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursals(id_sucursal);


--
-- PostgreSQL database dump complete
--

