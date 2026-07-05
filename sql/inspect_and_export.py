import sqlite3
import os

PROJECT_DIR = r"c:\Users\CSMBD\Downloads\business-website\project"
DB_PATH = os.path.join(PROJECT_DIR, 'database.sqlite')
OUTPUT_SQL_PATH = os.path.join(PROJECT_DIR, 'sql', 'supabase_setup.sql')

def escape_str(val):
    if val is None:
        return 'NULL'
    return "'" + str(val).replace("'", "''") + "'"

def map_type_to_postgres(col_name, col_type, is_pk):
    if is_pk:
        return "SERIAL PRIMARY KEY"
    
    col_type_upper = col_type.upper()
    
    if "INT" in col_type_upper:
        return "INT"
    if "TEXT" in col_type_upper:
        return "TEXT"
    if "VARCHAR" in col_type_upper:
        # Preserve VARCHAR limit if any, e.g., VARCHAR(255)
        return col_type_upper
    if "DATE" in col_type_upper:
        return "DATE"
    if "TIMESTAMP" in col_type_upper or "DATETIME" in col_type_upper:
        return "TIMESTAMP WITH TIME ZONE"
    
    return "VARCHAR(255)"  # fallback

def generate_postgres_sql():
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    # Get all tables
    cursor.execute("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")
    tables = [r[0] for r in cursor.fetchall()]
    
    out = []
    out.append("-- ========================================================")
    out.append("-- Supabase PostgreSQL Setup & Data Seed (Auto-generated)")
    out.append("-- Copy and paste this directly into your Supabase SQL Editor")
    out.append("-- ========================================================\n")
    
    # We want to create tables first, then inserts.
    # To handle foreign key dependencies (page_sections -> pages), we drop tables in reverse order if they exist, or just create them with IF NOT EXISTS.
    # Wait, page_sections has FOREIGN KEY (page_id) REFERENCES pages(id).
    # So pages must be created before page_sections!
    # Let's sort tables so dependencies are created first:
    # "pages" before "page_sections".
    if "pages" in tables and "page_sections" in tables:
        tables.remove("page_sections")
        tables.append("page_sections") # Ensure page_sections is created last!
        
    for table in tables:
        out.append(f"-- DROP TABLE IF EXISTS {table} CASCADE;")
        out.append(f"DROP TABLE IF EXISTS {table} CASCADE;")
        
    out.append("\n-- Create Tables")
    
    for table in tables:
        out.append(f"-- Schema for {table}")
        cursor.execute(f"PRAGMA table_info({table})")
        cols_info = cursor.fetchall()
        
        col_defs = []
        for cid, name, col_type, notnull, dflt_value, pk in cols_info:
            pg_type = map_type_to_postgres(name, col_type, pk == 1)
            
            def_str = f"    {name} {pg_type}"
            if notnull == 1 and pk != 1:
                def_str += " NOT NULL"
            if dflt_value is not null and pk != 1:
                # SQLite defaults can sometimes have quotes or keywords, e.g. CURRENT_TIMESTAMP
                if dflt_value.upper() == 'CURRENT_TIMESTAMP':
                    def_str += " DEFAULT CURRENT_TIMESTAMP"
                else:
                    def_str += f" DEFAULT {dflt_value}"
            col_defs.append(def_str)
            
        # Add Foreign keys manually if needed
        if table == 'page_sections':
            col_defs.append("    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE")
            
        schema = f"CREATE TABLE {table} (\n" + ",\n".join(col_defs) + "\n);"
        out.append(schema)
        out.append("")

    # Seed data
    out.append("-- Seed Data")
    for table in tables:
        cursor.execute(f"PRAGMA table_info({table})")
        cols = [c[1] for c in cursor.fetchall()]
        
        cursor.execute(f"SELECT * FROM {table}")
        rows = cursor.fetchall()
        
        if rows:
            out.append(f"-- Data for {table}")
            col_list = ", ".join(cols)
            out.append(f"INSERT INTO {table} ({col_list}) VALUES")
            values_list = []
            for row in rows:
                val_strs = [escape_str(v) for v in row]
                values_list.append(f"    ({', '.join(val_strs)})")
            out.append(",\n".join(values_list) + ";")
        else:
            out.append(f"-- No data for {table}")
        out.append("")

    # Reset Sequences
    out.append("-- Reset ID Sequences for PostgreSQL Serial Types")
    for table in tables:
        out.append(f"SELECT setval(pg_get_serial_sequence('{table}', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM {table};")

    with open(OUTPUT_SQL_PATH, 'w', encoding='utf-8') as f:
        f.write("\n".join(out))
    
    conn.close()
    print(f"Successfully generated dynamic PostgreSQL migration script at {OUTPUT_SQL_PATH}")

if __name__ == "__main__":
    import builtins
    builtins.null = None # to handle is not null check
    generate_postgres_sql()
